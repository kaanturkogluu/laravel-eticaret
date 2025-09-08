<?php

namespace App\Http\Controllers;

use App\Mail\PaymentSuccessMail;
use App\Models\PaymentProvider;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PaymentController extends Controller
{
    /**
     * Ödeme işlemini başlat
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_provider' => 'required|string',
            'payment_method' => 'required|string|in:credit_card,bank_transfer,wallet,cash_on_delivery',
            'installment' => 'nullable|integer|min:1|max:12'
        ]);

        try {
            $order = \App\Models\Order::findOrFail($request->order_id);
            $provider = PaymentProvider::where('code', $request->payment_provider)
                ->where('is_active', true)
                ->firstOrFail();

            // Tutar kontrolü
            if (!$provider->isAmountValid($order->total)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu tutar için ödeme sağlayıcısı kullanılamaz.'
                ], 400);
            }

            $service = PaymentServiceFactory::create($provider);
            
            $paymentData = [
                'payment_method' => $request->payment_method,
                'installment' => $request->installment ?? 1,
                'metadata' => [
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'referer' => $request->header('referer')
                ]
            ];

            $result = $service->initiatePayment($order, $paymentData);

            if ($result['success']) {
                return response()->json($result);
            } else {
                return response()->json($result, 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme işlemi başlatılamadı.'
            ], 500);
        }
    }

    /**
     * Ödeme durumunu kontrol et
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string'
        ]);

        try {
            $transaction = PaymentTransaction::where('transaction_id', $request->transaction_id)->firstOrFail();
            $service = PaymentServiceFactory::create($transaction->paymentProvider);
            
            $result = $service->checkPaymentStatus($transaction);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Payment status check failed', [
                'error' => $e->getMessage(),
                'transaction_id' => $request->transaction_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Ödeme durumu kontrol edilemedi.'
            ], 500);
        }
    }

    /**
     * Ödeme başarı sayfası
     */
    public function success(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $orderId = $request->input('order_id');

        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        } elseif ($orderId) {
            $transaction = PaymentTransaction::where('order_id', $orderId)->latest()->first();
        } else {
            return redirect()->route('home')->with('error', 'Geçersiz ödeme bilgisi.');
        }

        if (!$transaction) {
            return redirect()->route('home')->with('error', 'Ödeme işlemi bulunamadı.');
        }

        // Ödeme başarılı maili gönder
        if ($transaction->status === 'completed' && $transaction->order) {
            try {
                $user = $transaction->order->user;
                Mail::to($user->email)->send(new PaymentSuccessMail($transaction->order, $transaction, $user));
            } catch (\Exception $e) {
                Log::error('Payment success mail failed: ' . $e->getMessage());
            }
        }

        return view('payment.success', compact('transaction'));
    }

    /**
     * Ödeme başarısız sayfası
     */
    public function failure(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $orderId = $request->input('order_id');
        $error = $request->input('error', 'Ödeme işlemi başarısız oldu.');

        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        } elseif ($orderId) {
            $transaction = PaymentTransaction::where('order_id', $orderId)->latest()->first();
        } else {
            return redirect()->route('home')->with('error', $error);
        }

        return view('payment.failure', compact('transaction', 'error'));
    }

    /**
     * Ödeme iptal sayfası
     */
    public function cancel(Request $request)
    {
        $transactionId = $request->input('transaction_id');
        $orderId = $request->input('order_id');

        if ($transactionId) {
            $transaction = PaymentTransaction::where('transaction_id', $transactionId)->first();
        } elseif ($orderId) {
            $transaction = PaymentTransaction::where('order_id', $orderId)->latest()->first();
        } else {
            return redirect()->route('home')->with('error', 'Ödeme işlemi bulunamadı.');
        }

        if ($transaction) {
            $transaction->markAsCancelled();
        }

        return view('payment.cancel', compact('transaction'));
    }

    /**
     * Callback işlemi (tüm provider'lar için)
     */
    public function callback(Request $request, string $provider)
    {
        try {
            $paymentProvider = PaymentProvider::where('code', $provider)
                ->where('is_active', true)
                ->firstOrFail();

            $service = PaymentServiceFactory::create($paymentProvider);
            $result = $service->handleCallback($request);

            if ($result['success']) {
                return response()->json(['status' => 'success']);
            } else {
                Log::warning('Payment callback failed', [
                    'provider' => $provider,
                    'result' => $result,
                    'request' => $request->all()
                ]);
                
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment callback exception', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Callback işlemi başarısız'], 500);
        }
    }

    /**
     * Webhook işlemi (tüm provider'lar için)
     */
    public function webhook(Request $request, string $provider)
    {
        try {
            $paymentProvider = PaymentProvider::where('code', $provider)
                ->where('is_active', true)
                ->firstOrFail();

            // IP doğrulaması (opsiyonel)
            $allowedIps = $paymentProvider->getConfig('webhook_allowed_ips');
            if ($allowedIps && !$this->verifyIpAddress($request->ip(), $allowedIps)) {
                Log::warning('Webhook IP not allowed', [
                    'provider' => $provider,
                    'ip' => $request->ip(),
                    'allowed_ips' => $allowedIps
                ]);
                
                return response()->json(['status' => 'error', 'message' => 'IP not allowed'], 403);
            }

            $service = PaymentServiceFactory::create($paymentProvider);
            $result = $service->handleWebhook($request);

            if ($result['success']) {
                return response()->json(['status' => 'success']);
            } else {
                Log::warning('Payment webhook failed', [
                    'provider' => $provider,
                    'result' => $result,
                    'request' => $request->all()
                ]);
                
                return response()->json(['status' => 'error', 'message' => $result['message']], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment webhook exception', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json(['status' => 'error', 'message' => 'Webhook işlemi başarısız'], 500);
        }
    }

    /**
     * IP adresi doğrula
     */
    private function verifyIpAddress(string $ip, string $allowedIps): bool
    {
        $allowedIpList = explode(',', $allowedIps);
        
        foreach ($allowedIpList as $allowedIp) {
            $allowedIp = trim($allowedIp);
            if ($ip === $allowedIp || $this->ipInRange($ip, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * IP aralığı kontrolü
     */
    private function ipInRange(string $ip, string $range): bool
    {
        if (strpos($range, '/') === false) {
            return $ip === $range;
        }

        list($subnet, $bits) = explode('/', $range);
        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $bits);
        $subnet &= $mask;

        return ($ip & $mask) === $subnet;
    }

    /**
     * Mevcut ödeme sağlayıcılarını getir
     */
    public function getProviders(Request $request)
    {
        $currency = $request->input('currency', 'TRY');
        $amount = $request->input('amount');
        $paymentMethod = $request->input('payment_method');

        $query = PaymentProvider::where('is_active', true);

        if ($currency) {
            $query->whereJsonContains('supported_currencies', strtoupper($currency));
        }

        if ($paymentMethod) {
            $query->whereJsonContains('supported_payment_methods', $paymentMethod);
        }

        $providers = $query->orderBy('sort_order')->get();

        // Tutar filtresi
        if ($amount) {
            $providers = $providers->filter(function ($provider) use ($amount) {
                return $provider->isAmountValid($amount);
            });
        }

        return response()->json([
            'success' => true,
            'data' => $providers->map(function ($provider) use ($amount) {
                $commission = $amount ? $provider->calculateCommission($amount) : 0;
                
                return [
                    'id' => $provider->id,
                    'code' => $provider->code,
                    'name' => $provider->name,
                    'description' => $provider->description,
                    'logo_url' => $provider->logo_url,
                    'commission_rate' => $provider->commission_rate,
                    'commission_fixed' => $provider->commission_fixed,
                    'commission_amount' => $commission,
                    'test_mode' => $provider->test_mode,
                    'supported_currencies' => $provider->supported_currencies,
                    'supported_payment_methods' => $provider->supported_payment_methods,
                    'min_amount' => $provider->min_amount,
                    'max_amount' => $provider->max_amount
                ];
            })
        ]);
    }
}
