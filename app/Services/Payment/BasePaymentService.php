<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\PaymentProvider;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;

abstract class BasePaymentService
{
    protected PaymentProvider $provider;
    protected array $config;

    public function __construct(PaymentProvider $provider)
    {
        $this->provider = $provider;
        $this->config = $provider->config ?? [];
    }

    /**
     * Ödeme işlemini başlat
     */
    abstract public function initiatePayment(Order $order, array $paymentData = []): array;

    /**
     * Ödeme durumunu kontrol et
     */
    abstract public function checkPaymentStatus(PaymentTransaction $transaction): array;

    /**
     * Callback işlemi
     */
    abstract public function handleCallback(Request $request): array;

    /**
     * Webhook işlemi
     */
    abstract public function handleWebhook(Request $request): array;

    /**
     * İade işlemi
     */
    abstract public function refund(PaymentTransaction $transaction, float $amount = null): array;

    /**
     * Bağlantı testi
     */
    public function testConnection(): array
    {
        try {
            // Varsayılan test - sadece konfigürasyon kontrolü
            if (!$this->provider->isConfigured()) {
                return [
                    'success' => false,
                    'message' => 'Konfigürasyon eksik. Gerekli alanları doldurun.',
                    'details' => [
                        'configured' => false,
                        'missing_fields' => $this->getMissingConfigFields()
                    ]
                ];
            }

            // API URL kontrolü
            $apiUrl = $this->getApiUrl();
            if (empty($apiUrl)) {
                return [
                    'success' => false,
                    'message' => 'API URL konfigürasyonu eksik.',
                    'details' => [
                        'configured' => false,
                        'missing_fields' => ['api_url']
                    ]
                ];
            }

            return [
                'success' => true,
                'message' => 'Konfigürasyon tamamlandı. Gerçek bağlantı testi için ödeme işlemi yapın.',
                'details' => [
                    'configured' => true,
                    'test_mode' => $this->isTestMode(),
                    'api_url' => $apiUrl,
                    'provider' => $this->provider->name
                ]
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Test sırasında hata oluştu: ' . $e->getMessage(),
                'details' => [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ];
        }
    }

    /**
     * Test modunda mı?
     */
    protected function isTestMode(): bool
    {
        return $this->provider->test_mode;
    }

    /**
     * API URL'i al
     */
    protected function getApiUrl(string $endpoint = ''): string
    {
        $baseUrl = $this->isTestMode() 
            ? $this->getConfig('test_api_url') 
            : $this->getConfig('live_api_url');

        return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }

    /**
     * Konfigürasyon değeri al
     */
    protected function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * HTTP isteği gönder
     */
    protected function makeHttpRequest(string $url, array $data = [], string $method = 'POST', array $headers = []): array
    {
        $defaultHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $headers = array_merge($defaultHeaders, $headers);

        $client = new \GuzzleHttp\Client();
        
        try {
            $options = [
                'headers' => $headers,
                'timeout' => 30,
            ];

            if ($method === 'POST' || $method === 'PUT') {
                $options['json'] = $data;
            } elseif ($method === 'GET' && !empty($data)) {
                $options['query'] = $data;
            }

            $response = $client->request($method, $url, $options);
            
            return [
                'success' => true,
                'status_code' => $response->getStatusCode(),
                'data' => json_decode($response->getBody()->getContents(), true),
                'headers' => $response->getHeaders()
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status_code' => $e->getCode(),
                'data' => null
            ];
        }
    }

    /**
     * Payment transaction oluştur
     */
    protected function createPaymentTransaction(Order $order, array $data = []): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'payment_provider_id' => $this->provider->id,
            'transaction_id' => PaymentTransaction::generateTransactionId(),
            'amount' => $order->total,
            'currency' => $order->currency,
            'payment_method' => $data['payment_method'] ?? 'credit_card',
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    /**
     * Payment transaction güncelle
     */
    protected function updatePaymentTransaction(PaymentTransaction $transaction, array $data): void
    {
        $transaction->update($data);
    }

    /**
     * Başarılı yanıt döndür
     */
    protected function successResponse(array $data = [], string $message = 'İşlem başarılı'): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    /**
     * Hata yanıtı döndür
     */
    protected function errorResponse(string $message, array $data = [], int $code = 400): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code
        ];
    }

    /**
     * Güvenli log kaydı
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        // Hassas bilgileri maskele
        $maskedContext = $this->maskSensitiveData($context);
        
        \Log::channel('payment')->$level($message, $maskedContext);
    }

    /**
     * Hassas verileri maskele
     */
    protected function maskSensitiveData(array $data): array
    {
        $sensitiveKeys = [
            'password', 'token', 'key', 'secret', 'api_key', 'private_key',
            'card_number', 'cvv', 'cvc', 'expiry', 'pin'
        ];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->maskSensitiveData($value);
            } elseif (in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '***MASKED***';
            }
        }

        return $data;
    }

    /**
     * Tutar formatla
     */
    protected function formatAmount(float $amount, string $currency = 'TRY'): string
    {
        // Bazı gateway'ler kuruş cinsinden tutar bekler
        if (in_array($currency, ['TRY', 'TL'])) {
            return number_format($amount * 100, 0, '', ''); // Kuruş cinsinden
        }
        
        return number_format($amount, 2, '.', '');
    }

    /**
     * Hash oluştur
     */
    protected function createHash(array $data, string $secret): string
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
     * Hash doğrula
     */
    protected function verifyHash(array $data, string $receivedHash, string $secret): bool
    {
        $calculatedHash = $this->createHash($data, $secret);
        return hash_equals($calculatedHash, $receivedHash);
    }

    /**
     * IP adresi doğrula
     */
    protected function verifyIpAddress(string $allowedIps = null): bool
    {
        if (!$allowedIps) {
            return true;
        }

        $allowedIpList = explode(',', $allowedIps);
        $clientIp = request()->ip();

        foreach ($allowedIpList as $allowedIp) {
            $allowedIp = trim($allowedIp);
            if ($clientIp === $allowedIp || $this->ipInRange($clientIp, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * IP aralığı kontrolü
     */
    protected function ipInRange(string $ip, string $range): bool
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
     * Eksik konfigürasyon alanlarını getir
     */
    protected function getMissingConfigFields(): array
    {
        $requiredFields = ['merchant_id', 'api_key'];
        $missing = [];

        foreach ($requiredFields as $field) {
            if (empty($this->getConfig($field))) {
                $missing[] = $field;
            }
        }

        return $missing;
    }
}
