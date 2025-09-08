<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

class PaytrekService extends BasePaymentService
{
    public function initiatePayment(Order $order, array $paymentData = []): array
    {
        try {
            $transaction = $this->createPaymentTransaction($order, $paymentData);
            
            $apiUrl = $this->getApiUrl('api/v1/payment/initiate');
            $merchantId = $this->getConfig('merchant_id');
            $secretKey = $this->getConfig('secret_key');

            $requestData = [
                'merchant_id' => $merchantId,
                'order_id' => $transaction->transaction_id,
                'amount' => $this->formatAmount($order->total, $order->currency),
                'currency' => $order->currency,
                'customer_email' => $order->customer_email,
                'customer_name' => $order->customer_name,
                'description' => "Sipariş #{$order->order_number}",
                'success_url' => $this->getConfig('success_url'),
                'fail_url' => $this->getConfig('fail_url'),
                'callback_url' => $this->getConfig('callback_url')
            ];

            $requestData['signature'] = $this->createHash($requestData, $secretKey);

            $response = $this->makeHttpRequest($apiUrl, $requestData);

            if ($response['success'] && isset($response['data']['payment_url'])) {
                $this->updatePaymentTransaction($transaction, [
                    'external_transaction_id' => $response['data']['transaction_id'],
                    'status' => 'processing',
                    'gateway_response' => $response['data']
                ]);

                return $this->successResponse([
                    'transaction_id' => $transaction->transaction_id,
                    'payment_url' => $response['data']['payment_url']
                ], 'Paytrek ödeme işlemi başlatıldı');
            }

            $this->updatePaymentTransaction($transaction, [
                'status' => 'failed',
                'gateway_error' => $response
            ]);

            return $this->errorResponse('Paytrek ödeme işlemi başlatılamadı', $response);

        } catch (\Exception $e) {
            return $this->errorResponse('Paytrek ödeme işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    public function checkPaymentStatus(PaymentTransaction $transaction): array
    {
        try {
            $apiUrl = $this->getApiUrl('api/v1/payment/status');
            $merchantId = $this->getConfig('merchant_id');
            $secretKey = $this->getConfig('secret_key');

            $requestData = [
                'merchant_id' => $merchantId,
                'order_id' => $transaction->transaction_id
            ];

            $requestData['signature'] = $this->createHash($requestData, $secretKey);

            $response = $this->makeHttpRequest($apiUrl, $requestData, 'GET');

            if ($response['success']) {
                $status = $this->mapStatus($response['data']['status']);
                
                $this->updatePaymentTransaction($transaction, [
                    'status' => $status,
                    'gateway_response' => $response['data']
                ]);

                return $this->successResponse(['status' => $status, 'data' => $response['data']]);
            }

            return $this->errorResponse('Paytrek durum kontrolü başarısız', $response);

        } catch (\Exception $e) {
            return $this->errorResponse('Paytrek durum kontrolü sırasında hata oluştu: ' . $e->getMessage());
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
            return $this->errorResponse('Paytrek callback işlemi sırasında hata oluştu: ' . $e->getMessage());
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
            $apiUrl = $this->getApiUrl('api/v1/payment/refund');
            $merchantId = $this->getConfig('merchant_id');
            $secretKey = $this->getConfig('secret_key');

            $requestData = [
                'merchant_id' => $merchantId,
                'order_id' => $transaction->transaction_id,
                'amount' => $this->formatAmount($refundAmount, $transaction->currency),
                'reason' => 'İade işlemi'
            ];

            $requestData['signature'] = $this->createHash($requestData, $secretKey);

            $response = $this->makeHttpRequest($apiUrl, $requestData);

            if ($response['success']) {
                $transaction->refund($refundAmount, 'Paytrek iade işlemi');
                return $this->successResponse(['refund_amount' => $refundAmount], 'Paytrek iade işlemi başarılı');
            }

            return $this->errorResponse('Paytrek iade işlemi başarısız', $response);

        } catch (\Exception $e) {
            return $this->errorResponse('Paytrek iade işlemi sırasında hata oluştu: ' . $e->getMessage());
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
