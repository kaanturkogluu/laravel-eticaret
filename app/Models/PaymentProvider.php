<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentProvider extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'logo_url',
        'is_active',
        'sort_order',
        'config',
        'supported_currencies',
        'supported_payment_methods',
        'min_amount',
        'max_amount',
        'commission_rate',
        'commission_fixed',
        'test_mode',
        'webhook_url',
        'callback_url',
        'return_url',
        'cancel_url'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'test_mode' => 'boolean',
        'config' => 'array',
        'supported_currencies' => 'array',
        'supported_payment_methods' => 'array',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'commission_rate' => 'decimal:4',
        'commission_fixed' => 'decimal:2',
        'sort_order' => 'integer'
    ];

    /**
     * Payment transactions ilişkisi
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Aktif ödeme sağlayıcılarını getir
     */
    public static function getActiveProviders()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Para birimini destekleyen sağlayıcıları getir
     */
    public static function getProvidersForCurrency(string $currency)
    {
        return static::where('is_active', true)
            ->whereJsonContains('supported_currencies', strtoupper($currency))
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Ödeme yöntemini destekleyen sağlayıcıları getir
     */
    public static function getProvidersForPaymentMethod(string $paymentMethod)
    {
        return static::where('is_active', true)
            ->whereJsonContains('supported_payment_methods', $paymentMethod)
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Tutar aralığını kontrol et
     */
    public function isAmountValid(float $amount): bool
    {
        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        if ($this->max_amount && $amount > $this->max_amount) {
            return false;
        }

        return true;
    }

    /**
     * Komisyon hesapla
     */
    public function calculateCommission(float $amount): float
    {
        $commission = 0;

        // Yüzdelik komisyon
        if ($this->commission_rate > 0) {
            $commission += $amount * ($this->commission_rate / 100);
        }

        // Sabit komisyon
        if ($this->commission_fixed > 0) {
            $commission += $this->commission_fixed;
        }

        return round($commission, 2);
    }

    /**
     * Test modunda mı kontrol et
     */
    public function isTestMode(): bool
    {
        return $this->test_mode;
    }

    /**
     * Konfigürasyon değeri al
     */
    public function getConfig(string $key, $default = null)
    {
        return data_get($this->config, $key, $default);
    }

    /**
     * Konfigürasyon değeri ayarla
     */
    public function setConfig(string $key, $value): void
    {
        $config = $this->config ?? [];
        data_set($config, $key, $value);
        $this->config = $config;
    }

    /**
     * Logo URL'i al
     */
    public function getLogoUrlAttribute($value)
    {
        if ($value) {
            return $value;
        }

        // Varsayılan logo URL'leri
        $defaultLogos = [
            'payu' => '/images/payment-logos/payu.svg',
            'sipay' => '/images/payment-logos/sipay.svg',
            'mokapos' => '/images/payment-logos/mokapos.svg',
            'paynet' => '/images/payment-logos/paynet.svg',
            'odeal' => '/images/payment-logos/odeal.svg',
            'papara' => '/images/payment-logos/papara.svg',
            'paycell' => '/images/payment-logos/paycell.svg',
            'hepsipay' => '/images/payment-logos/hepsipay.svg',
            'esnekpos' => '/images/payment-logos/esnekpos.svg',
            'ininal' => '/images/payment-logos/ininal.svg',
            'paytrek' => '/images/payment-logos/paytrek.svg',
        ];

        return $defaultLogos[$this->code] ?? '/images/payment-logos/default.svg';
    }

    /**
     * Durum etiketi
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Pasif';
    }

    /**
     * Durum rengi
     */
    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'danger';
    }

    /**
     * API URL'i al (test/canlı moda göre)
     */
    public function getApiUrl(): string
    {
        if ($this->test_mode) {
            return $this->getConfig('test_api_url', '');
        }
        
        return $this->getConfig('live_api_url', '');
    }

    /**
     * Merchant ID al
     */
    public function getMerchantId(): string
    {
        return $this->getConfig('merchant_id', '');
    }

    /**
     * API Key al
     */
    public function getApiKey(): string
    {
        return $this->getConfig('api_key', '');
    }

    /**
     * Secret Key al
     */
    public function getSecretKey(): string
    {
        return $this->getConfig('secret_key', '');
    }

    /**
     * Konfigürasyon tamamlanmış mı kontrol et
     */
    public function isConfigured(): bool
    {
        $requiredFields = ['merchant_id', 'api_key'];
        
        foreach ($requiredFields as $field) {
            if (empty($this->getConfig($field))) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Scope query - aktif sağlayıcılar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope query - test modu
     */
    public function scopeTestMode($query)
    {
        return $query->where('test_mode', true);
    }

    /**
     * Scope query - canlı mod
     */
    public function scopeLiveMode($query)
    {
        return $query->where('test_mode', false);
    }

    /**
     * Scope query - para birimi desteği
     */
    public function scopeForCurrency($query, string $currency)
    {
        return $query->whereJsonContains('supported_currencies', strtoupper($currency));
    }

    /**
     * Scope query - ödeme yöntemi desteği
     */
    public function scopeForPaymentMethod($query, string $paymentMethod)
    {
        return $query->whereJsonContains('supported_payment_methods', $paymentMethod);
    }
}
