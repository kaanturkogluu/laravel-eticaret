<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class SipayService extends BasePaymentService
{
    /**
     * Sipay ödeme işlemini başlat
     */
    public function initiatePayment(Order $order, array $paymentData = []): array
    {
        try {
            $this->log('info', 'Sipay payment initiation started', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $order->total
            ]);

            $transaction = $this->createPaymentTransaction($order, $paymentData);

            $apiUrl = $this->getApiUrl('api/v1/payment/init');
            $merchantKey = $this->getConfig('merchant_key');
            $merchantSecret = $this->getConfig('merchant_secret');

            $requestData = [
                'merchant_key' => $merchantKey,
                'order_id' => $transaction->transaction_id,
                'amount' => $this->formatAmount($order->total, $order->currency),
                'currency' => $order->currency,
                'customer_email' => $order->customer_email,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'description' => "Sipariş #{$order->order_number}",
                'success_url' => $this->getConfig('success_url'),
                'fail_url' => $this->getConfig('fail_url'),
                'callback_url' => $this->getConfig('callback_url'),
                'installment' => $paymentData['installment'] ?? 1,
                'payment_method' => $paymentData['payment_method'] ?? 'credit_card'
            ];

            // Hash oluştur
            $requestData['hash'] = $this->createSipayHash($requestData, $merchantSecret);

            $response = $this->makeHttpRequest($apiUrl, $requestData);

            if ($response['success'] && isset($response['data']['payment_url'])) {
                $this->updatePaymentTransaction($transaction, [
                    'external_transaction_id' => $response['data']['transaction_id'] ?? null,
                    'status' => 'processing',
                    'gateway_response' => $response['data']
                ]);

                $this->log('info', 'Sipay payment initiated successfully', [
                    'transaction_id' => $transaction->transaction_id
                ]);

                return $this->successResponse([
                    'transaction_id' => $transaction->transaction_id,
                    'payment_url' => $response['data']['payment_url'],
                    'redirect_url' => $response['data']['payment_url']
                ], 'Sipay ödeme işlemi başlatıldı');
            } else {
                $this->updatePaymentTransaction($transaction, [
                    'status' => 'failed',
                    'gateway_error' => $response
                ]);

                return $this->errorResponse('Sipay ödeme işlemi başlatılamadı', $response);
            }

        } catch (\Exception $e) {
            $this->log('error', 'Sipay payment initiation exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Sipay ödeme işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sipay ödeme durumunu kontrol et
     */
    public function checkPaymentStatus(PaymentTransaction $transaction): array
    {
        try {
            $apiUrl = $this->getApiUrl('api/v1/payment/status');
            $merchantKey = $this->getConfig('merchant_key');
            $merchantSecret = $this->getConfig('merchant_secret');

            $requestData = [
                'merchant_key' => $merchantKey,
                'order_id' => $transaction->transaction_id
            ];

            $requestData['hash'] = $this->createSipayHash($requestData, $merchantSecret);

            $response = $this->makeHttpRequest($apiUrl, $requestData, 'GET');

            if ($response['success']) {
                $status = $this->mapSipayStatus($response['data']['status']);
                
                $this->updatePaymentTransaction($transaction, [
                    'status' => $status,
                    'gateway_response' => $response['data']
                ]);

                return $this->successResponse([
                    'status' => $status,
                    'data' => $response['data']
                ]);
            }

            return $this->errorResponse('Sipay durum kontrolü başarısız', $response);

        } catch (\Exception $e) {
            $this->log('error', 'Sipay status check exception', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Sipay durum kontrolü sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sipay callback işlemi
     */
    public function handleCallback(Request $request): array
    {
        try {
            $this->log('info', 'Sipay callback received', $request->all());

            $orderId = $request->input('order_id');
            $status = $request->input('status');
            $hash = $request->input('hash');

            $transaction = PaymentTransaction::where('transaction_id', $orderId)->first();
            
            if (!$transaction) {
                return $this->errorResponse('Transaction bulunamadı');
            }

            // Hash doğrula
            $calculatedHash = $this->createSipayHash($request->except('hash')->toArray(), $this->getConfig('merchant_secret'));
            
            if (!hash_equals($calculatedHash, $hash)) {
                $this->log('error', 'Sipay callback hash verification failed', [
                    'transaction_id' => $transaction->transaction_id
                ]);
                
                return $this->errorResponse('Hash doğrulaması başarısız');
            }

            $mappedStatus = $this->mapSipayStatus($status);
            
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

            return $this->successResponse([
                'transaction_id' => $transaction->transaction_id,
                'status' => $mappedStatus
            ]);

        } catch (\Exception $e) {
            $this->log('error', 'Sipay callback exception', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return $this->errorResponse('Sipay callback işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sipay webhook işlemi
     */
    public function handleWebhook(Request $request): array
    {
        return $this->handleCallback($request);
    }

    /**
     * Sipay iade işlemi
     */
    public function refund(PaymentTransaction $transaction, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $transaction->amount;
            $apiUrl = $this->getApiUrl('api/v1/payment/refund');
            
            $merchantKey = $this->getConfig('merchant_key');
            $merchantSecret = $this->getConfig('merchant_secret');

            $requestData = [
                'merchant_key' => $merchantKey,
                'order_id' => $transaction->transaction_id,
                'amount' => $this->formatAmount($refundAmount, $transaction->currency),
                'reason' => 'İade işlemi'
            ];

            $requestData['hash'] = $this->createSipayHash($requestData, $merchantSecret);

            $response = $this->makeHttpRequest($apiUrl, $requestData);

            if ($response['success']) {
                $transaction->refund($refundAmount, 'Sipay iade işlemi');
                
                $this->log('info', 'Sipay refund successful', [
                    'transaction_id' => $transaction->transaction_id,
                    'refund_amount' => $refundAmount
                ]);

                return $this->successResponse([
                    'refund_amount' => $refundAmount,
                    'data' => $response['data']
                ], 'Sipay iade işlemi başarılı');
            }

            return $this->errorResponse('Sipay iade işlemi başarısız', $response);

        } catch (\Exception $e) {
            $this->log('error', 'Sipay refund exception', [
                'transaction_id' => $transaction->transaction_id,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse('Sipay iade işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sipay hash oluştur
     */
    private function createSipayHash(array $data, string $secret): string
    {
        ksort($data);
        $hashString = '';
        
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $hashString .= $key . '=' . $value . '&';
            }
        }
        
        $hashString = rtrim($hashString, '&');
        $hashString .= $secret;
        
        return hash('sha256', $hashString);
    }

    /**
     * Sipay durumunu sistem durumuna map et
     */
    private function mapSipayStatus(string $sipayStatus): string
    {
        return match($sipayStatus) {
            'pending' => 'pending',
            'processing' => 'processing',
            'success' => 'completed',
            'failed' => 'failed',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
    }
}
