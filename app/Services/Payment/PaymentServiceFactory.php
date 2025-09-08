<?php

namespace App\Services\Payment;

use App\Models\PaymentProvider;

class PaymentServiceFactory
{
    /**
     * Payment service sınıfları mapping
     */
    private static array $serviceMap = [
        'payu' => PayUService::class,
        'sipay' => SipayService::class,
        'mokapos' => MokaposService::class,
        'paynet' => PaynetService::class,
        'odeal' => OdealService::class,
        'papara' => PaparaService::class,
        'paycell' => PaycellService::class,
        'hepsipay' => HepsipayService::class,
        'esnekpos' => EsnekposService::class,
        'ininal' => IninalService::class,
        'paytrek' => PaytrekService::class,
    ];

    /**
     * Payment provider'a göre service oluştur
     */
    public static function create(PaymentProvider $provider): BasePaymentService
    {
        $code = strtolower($provider->code);
        
        if (!isset(self::$serviceMap[$code])) {
            throw new \InvalidArgumentException("Payment service not found for provider: {$provider->code}");
        }

        $serviceClass = self::$serviceMap[$code];
        
        if (!class_exists($serviceClass)) {
            throw new \RuntimeException("Payment service class not found: {$serviceClass}");
        }

        return new $serviceClass($provider);
    }

    /**
     * Provider code'a göre service oluştur
     */
    public static function createByCode(string $code): BasePaymentService
    {
        $provider = PaymentProvider::where('code', $code)->first();
        
        if (!$provider) {
            throw new \InvalidArgumentException("Payment provider not found: {$code}");
        }

        return self::create($provider);
    }

    /**
     * Desteklenen provider kodlarını getir
     */
    public static function getSupportedProviders(): array
    {
        return array_keys(self::$serviceMap);
    }

    /**
     * Provider destekleniyor mu?
     */
    public static function isProviderSupported(string $code): bool
    {
        return isset(self::$serviceMap[strtolower($code)]);
    }

    /**
     * Tüm aktif provider'lar için service'leri getir
     */
    public static function getActiveServices(): array
    {
        $services = [];
        $providers = PaymentProvider::where('is_active', true)->get();

        foreach ($providers as $provider) {
            if (self::isProviderSupported($provider->code)) {
                $services[$provider->code] = self::create($provider);
            }
        }

        return $services;
    }

    /**
     * Para birimini destekleyen service'leri getir
     */
    public static function getServicesForCurrency(string $currency): array
    {
        $services = [];
        $providers = PaymentProvider::getProvidersForCurrency($currency);

        foreach ($providers as $provider) {
            if (self::isProviderSupported($provider->code)) {
                $services[$provider->code] = self::create($provider);
            }
        }

        return $services;
    }

    /**
     * Ödeme yöntemini destekleyen service'leri getir
     */
    public static function getServicesForPaymentMethod(string $paymentMethod): array
    {
        $services = [];
        $providers = PaymentProvider::getProvidersForPaymentMethod($paymentMethod);

        foreach ($providers as $provider) {
            if (self::isProviderSupported($provider->code)) {
                $services[$provider->code] = self::create($provider);
            }
        }

        return $services;
    }

    /**
     * Tutar aralığını destekleyen service'leri getir
     */
    public static function getServicesForAmount(float $amount, string $currency = 'TRY'): array
    {
        $services = [];
        $providers = PaymentProvider::getProvidersForCurrency($currency);

        foreach ($providers as $provider) {
            if ($provider->isAmountValid($amount) && self::isProviderSupported($provider->code)) {
                $services[$provider->code] = self::create($provider);
            }
        }

        return $services;
    }

    /**
     * En uygun service'i seç (komisyon oranına göre)
     */
    public static function getBestService(float $amount, string $currency = 'TRY'): ?BasePaymentService
    {
        $services = self::getServicesForAmount($amount, $currency);
        
        if (empty($services)) {
            return null;
        }

        $bestService = null;
        $lowestCommission = PHP_FLOAT_MAX;

        foreach ($services as $service) {
            $commission = $service->getProvider()->calculateCommission($amount);
            $totalCost = $amount + $commission;

            if ($totalCost < $lowestCommission) {
                $lowestCommission = $totalCost;
                $bestService = $service;
            }
        }

        return $bestService;
    }

    /**
     * Service map'i güncelle
     */
    public static function registerService(string $code, string $serviceClass): void
    {
        if (!is_subclass_of($serviceClass, BasePaymentService::class)) {
            throw new \InvalidArgumentException("Service class must extend BasePaymentService");
        }

        self::$serviceMap[strtolower($code)] = $serviceClass;
    }

    /**
     * Service map'ten kaldır
     */
    public static function unregisterService(string $code): void
    {
        unset(self::$serviceMap[strtolower($code)]);
    }
}
