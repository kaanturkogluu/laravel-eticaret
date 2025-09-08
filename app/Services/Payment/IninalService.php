<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class IninalService extends BasePaymentService
{
    public function initiatePayment(Order $order, array $paymentData = []): array
    {
        try {
            $transaction = $this->createPaymentTransaction($order, $paymentData);
            
            $apiUrl = $this->getApiUrl('api/v1/payment/create');
            $apiKey = $this->getConfig('api_key');
            $secretKey = $this->getConfig('secret_key');

            $requestData = [
                'amount' => $this->formatAmount($order->total, $order->currency),
                'currency' => $order->currency,
                'order_id' => $transaction->transaction_id,
                'customer_email' => $order->customer_email,
                'customer_name' => $order->customer_name,
                'description' => "Sipariş #{$order->order_number}",
                'success_url' => $this->getConfig('success_url'),
                'fail_url' => $this->getConfig('fail_url'),
                'callback_url' => $this->getConfig('callback_url')
            ];

            $requestData['signature'] = $this->createHash($requestData, $secretKey);

            $response = $this->makeHttpRequest($apiUrl, $requestData, 'POST', [
                'Authorization' => 'Bearer ' . $apiKey
            ]);

            if ($response['success'] && isset($response['data']['payment_url'])) {
                $this->updatePaymentTransaction($transaction, [
                    'external_transaction_id' => $response['data']['id'],
                    'status' => 'processing',
                    'gateway_response' => $response['data']
                ]);

                return $this->successResponse([
                    'transaction_id' => $transaction->transaction_id,
                    'payment_url' => $response['data']['payment_url']
                ], 'Ininal ödeme işlemi başlatıldı');
            }

            $this->updatePaymentTransaction($transaction, [
                'status' => 'failed',
                'gateway_error' => $response
            ]);

            return $this->errorResponse('Ininal ödeme işlemi başlatılamadı', $response);

        } catch (\Exception $e) {
            return $this->errorResponse('Ininal ödeme işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    public function checkPaymentStatus(PaymentTransaction $transaction): array
    {
        try {
            $apiUrl = $this->getApiUrl("api/v1/payment/{$transaction->external_transaction_id}");
            $apiKey = $this->getConfig('api_key');

            $response = $this->makeHttpRequest($apiUrl, [], 'GET', [
                'Authorization' => 'Bearer ' . $apiKey
            ]);

            if ($response['success']) {
                $status = $this->mapStatus($response['data']['status']);
                
                $this->updatePaymentTransaction($transaction, [
                    'status' => $status,
                    'gateway_response' => $response['data']
                ]);

                return $this->successResponse(['status' => $status, 'data' => $response['data']]);
            }

            return $this->errorResponse('Ininal durum kontrolü başarısız', $response);

        } catch (\Exception $e) {
            return $this->errorResponse('Ininal durum kontrolü sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    public function handleCallback(Request $request): array
    {
        try {
            $orderId = $request->input('order_id');
            $status = $request->input('status');
            $signature = $request->input('signature');

            $transaction = PaymentTransaction::where('transaction_id', $orderId)->first();
            
            if (!$transaction) {
                return $this->errorResponse('Transaction bulunamadı');
            }

            $calculatedSignature = $this->createHash($request->except('signature')->toArray(), $this->getConfig('secret_key'));
            
            if (!hash_equals($calculatedSignature, $signature)) {
                return $this->errorResponse('Signature doğrulaması başarısız');
            }

            $mappedStatus = $this->mapStatus($status);
            
            $this->updatePaymentTransaction($transaction, [
                'status' => $mappedStatus,
                'callback_data' => $request->all()
            ]);

            if ($mappedStatus === 'completed') {
                $transaction->order->update([
                    'payment_status' => 'paid',
                    'payment_reference' => $transaction->transaction_id,
                    'paid_at' => now(),
                    'status' => 'processing'
                ]);
            }

            return $this->successResponse([
                'transaction_id' => $transaction->transaction_id,
                'status' => $mappedStatus
            ]);

        } catch (\Exception $e) {
            return $this->errorResponse('Ininal callback işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    public function handleWebhook(Request $request): array
    {
        return $this->handleCallback($request);
    }

    public function refund(PaymentTransaction $transaction, float $amount = null): array
    {
        try {
            $refundAmount = $amount ?? $transaction->amount;
            $apiUrl = $this->getApiUrl("api/v1/payment/{$transaction->external_transaction_id}/refund");
            $apiKey = $this->getConfig('api_key');

            $requestData = [
                'amount' => $this->formatAmount($refundAmount, $transaction->currency),
                'reason' => 'İade işlemi'
            ];

            $response = $this->makeHttpRequest($apiUrl, $requestData, 'POST', [
                'Authorization' => 'Bearer ' . $apiKey
            ]);

            if ($response['success']) {
                $transaction->refund($refundAmount, 'Ininal iade işlemi');
                return $this->successResponse(['refund_amount' => $refundAmount], 'Ininal iade işlemi başarılı');
            }

            return $this->errorResponse('Ininal iade işlemi başarısız', $response);

        } catch (\Exception $e) {
            return $this->errorResponse('Ininal iade işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    private function mapStatus(string $status): string
    {
        return match($status) {
            'pending' => 'pending',
            'processing' => 'processing',
            'success', 'completed' => 'completed',
            'failed', 'error' => 'failed',
            'cancelled' => 'cancelled',
            default => 'pending'
        };
    }
}
