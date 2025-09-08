<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PayUService extends BasePaymentService
{
    /**
     * PayU ödeme işlemini başlat
     */
    public function initiatePayment(Order $order, array $paymentData = []): array
    {
        try {
            $this->log('info', 'PayU payment initiation started', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total
            ]);

            // Payment transaction oluştur
            $transaction = $this->createPaymentTransaction($order, $paymentData);

            // PayU API parametreleri
            $merchantId = $this->getConfig('merchant_id');
            $secretKey = $this->getConfig('secret_key');
            $apiUrl = $this->getApiUrl('api/v2/orders');

            $requestData = [
                'notifyUrl' => $this->getConfig('notify_url'),
                'continueUrl' => $this->getConfig('continue_url'),
                'customerIp' => request()->ip(),
                'merchantPosId' => $merchantId,
                'description' => "Sipariş #{$order->order_number}",
                'currencyCode' => $this->getCurrencyCode($order->currency),
                'totalAmount' => $this->formatAmount($order->total, $order->currency),
                'extOrderId' => $transaction->transaction_id,
                'buyer' => [
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                    'firstName' => explode(' ', $order->customer_name)[0],
                    'lastName' => implode(' ', array_slice(explode(' ', $order->customer_name), 1)),
                ],
                'products' => $this->buildProducts($order)
            ];

            // Hash oluştur
            $requestData['signature'] = $this->createPayUHash($requestData, $secretKey);

            // API isteği gönder
            $response = $this->makeHttpRequest($apiUrl, $requestData);

            if ($response['success'] && isset($response['data']['orderId'])) {
                // Transaction güncelle
                $this->updatePaymentTransaction($transaction, [
                    'external_transaction_id' => $response['data']['orderId'],
                    'status' => 'processing',
                    'gateway_response' => $response['data']
                ]);

                $this->log('info', 'PayU payment initiated successfully', [
                    'transaction_id' => $transaction->transaction_id,
                    'external_transaction_id' => $response['data']['orderId']
                ]);

                return $this->successResponse([
                    'transaction_id' => $transaction->transaction_id,
                    'external_transaction_id' => $response['data']['orderId'],
                    'redirect_url' => $response['data']['redirectUri'] ?? null,
                    'payment_url' => $response['data']['redirectUri'] ?? null
                ], 'PayU ödeme işlemi başlatıldı');
            } else {
                $this->updatePaymentTransaction($transaction, [
                    'status' => 'failed',
                    'gateway_error' => $response
                ]);

                $this->log('error', 'PayU payment initiation failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'error' => $response
                ]);

                return $this->errorResponse('PayU ödeme işlemi başlatılamadı', $response);
            }

        } catch (\Exception $e) {
            $this->log('error', 'PayU payment initiation exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('PayU ödeme işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * PayU ödeme durumunu kontrol et
     */
    public function checkPaymentStatus(PaymentTransaction $transaction): array
    {
        try {
            $apiUrl = $this->getApiUrl("api/v2/orders/{$transaction->external_transaction_id}");
            $merchantId = $this->getConfig('merchant_id');
            $secretKey = $this->getConfig('secret_key');

            $requestData = [
                'merchantPosId' => $merchantId,
                'orderId' => $transaction->external_transaction_id
            ];

            $requestData['signature'] = $this->createPayUHash($requestData, $secretKey);

            $response = $this->makeHttpRequest($apiUrl, $requestData, 'GET');

            if ($response['success']) {
                $status = $this->mapPayUStatus($response['data']['status']);
                
                $this->updatePaymentTransaction($transaction, [
                    'status' => $status,
                    'gateway_response' => $response['data']
                ]);

                return $this->successResponse([
                    'status' => $status,
                    'data' => $response['data']
                ]);
            }

            return $this->errorResponse('PayU durum kontrolü başarısız', $response);

        } catch (\Exception $e) {
            $this->log('error', 'PayU status check exception', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('PayU durum kontrolü sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * PayU callback işlemi
     */
    public function handleCallback(Request $request): array
    {
        try {
            $this->log('info', 'PayU callback received', $request->all());

            $orderId = $request->input('orderId');
            $status = $request->input('status');
            $signature = $request->input('signature');

            // Transaction bul
            $transaction = PaymentTransaction::where('external_transaction_id', $orderId)->first();
            
            if (!$transaction) {
                return $this->errorResponse('Transaction bulunamadı');
            }

            // Signature doğrula
            $calculatedSignature = $this->createPayUHash($request->except('signature')->toArray(), $this->getConfig('secret_key'));
            
            if (!hash_equals($calculatedSignature, $signature)) {
                $this->log('error', 'PayU callback signature verification failed', [
                    'transaction_id' => $transaction->transaction_id,
                    'received_signature' => $signature,
                    'calculated_signature' => $calculatedSignature
                ]);
                
                return $this->errorResponse('Signature doğrulaması başarısız');
            }

            $mappedStatus = $this->mapPayUStatus($status);
            
            $this->updatePaymentTransaction($transaction, [
                'status' => $mappedStatus,
                'callback_data' => $request->all()
            ]);

            // Sipariş durumunu güncelle
            if ($mappedStatus === 'completed') {
                $transaction->order->update([
                    'payment_status' => 'paid',
                    'payment_reference' => $transaction->transaction_id,
                    'paid_at' => now(),
                    'status' => 'processing'
                ]);
            } elseif ($mappedStatus === 'failed') {
                $transaction->order->update([
                    'payment_status' => 'failed'
                ]);
            }

            $this->log('info', 'PayU callback processed successfully', [
                'transaction_id' => $transaction->transaction_id,
                'status' => $mappedStatus
            ]);

            return $this->successResponse([
                'transaction_id' => $transaction->transaction_id,
                'status' => $mappedStatus
            ]);

        } catch (\Exception $e) {
            $this->log('error', 'PayU callback exception', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return $this->errorResponse('PayU callback işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * PayU webhook işlemi
     */
    public function handleWebhook(Request $request): array
    {
        // PayU webhook genellikle callback ile aynı işlemi yapar
        return $this->handleCallback($request);
    }

    /**
     * PayU iade işlemi
     */
    public function refund(PaymentTransaction $transaction, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $transaction->amount;
            $apiUrl = $this->getApiUrl('api/v2/orders/' . $transaction->external_transaction_id . '/refund');
            
            $merchantId = $this->getConfig('merchant_id');
            $secretKey = $this->getConfig('secret_key');

            $requestData = [
                'merchantPosId' => $merchantId,
                'orderId' => $transaction->external_transaction_id,
                'refund' => [
                    'amount' => $this->formatAmount($refundAmount, $transaction->currency),
                    'description' => 'İade işlemi'
                ]
            ];

            $requestData['signature'] = $this->createPayUHash($requestData, $secretKey);

            $response = $this->makeHttpRequest($apiUrl, $requestData);

            if ($response['success']) {
                $transaction->refund($refundAmount, 'PayU iade işlemi');
                
                $this->log('info', 'PayU refund successful', [
                    'transaction_id' => $transaction->transaction_id,
                    'refund_amount' => $refundAmount
                ]);

                return $this->successResponse([
                    'refund_amount' => $refundAmount,
                    'data' => $response['data']
                ], 'PayU iade işlemi başarılı');
            }

            return $this->errorResponse('PayU iade işlemi başarısız', $response);

        } catch (\Exception $e) {
            $this->log('error', 'PayU refund exception', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('PayU iade işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * PayU hash oluştur
     */
    private function createPayUHash(array $data, string $secretKey): string
    {
        // PayU'nun kendi hash algoritması
        $hashString = '';
        
        // Sıralı hash oluştur
        $this->sortRecursive($data);
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $hashString .= $this->arrayToString($value);
            } else {
                $hashString .= $value;
            }
        }
        
        $hashString .= $secretKey;
        
        return hash('sha256', $hashString);
    }

    /**
     * Recursive array sorting
     */
    private function sortRecursive(array &$array): void
    {
        ksort($array);
        foreach ($array as &$value) {
            if (is_array($value)) {
                $this->sortRecursive($value);
            }
        }
    }

    /**
     * Array to string conversion
     */
    private function arrayToString(array $array): string
    {
        $string = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $string .= $this->arrayToString($value);
            } else {
                $string .= $value;
            }
        }
        return $string;
    }

    /**
     * PayU durumunu sistem durumuna map et
     */
    private function mapPayUStatus(string $payuStatus): string
    {
        return match($payuStatus) {
            'NEW' => 'pending',
            'PENDING' => 'processing',
            'WAITING_FOR_CONFIRMATION' => 'processing',
            'COMPLETED' => 'completed',
            'CANCELED' => 'cancelled',
            'REJECTED' => 'failed',
            default => 'pending'
        };
    }

    /**
     * Para birimi kodunu PayU formatına çevir
     */
    private function getCurrencyCode(string $currency): string
    {
        return match(strtoupper($currency)) {
            'TRY', 'TL' => 'PLN', // PayU'da TRY yerine PLN kullanılır
            'USD' => 'USD',
            'EUR' => 'EUR',
            default => 'PLN'
        };
    }

    /**
     * Sipariş ürünlerini PayU formatına çevir
     */
    private function buildProducts(Order $order): array
    {
        $products = [];
        
        foreach ($order->items as $item) {
            $products[] = [
                'name' => $item->product_name,
                'unitPrice' => $this->formatAmount($item->unit_price, $order->currency),
                'quantity' => $item->quantity
            ];
        }

        return $products;
    }
}
