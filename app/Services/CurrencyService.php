<?php

namespace App\Services;

use App\Models\Currency;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class CurrencyService
{
    private $tcmbUrl = 'http://www.tcmb.gov.tr/kurlar/today.xml';

    /**
     * TCMB'den döviz kurlarını çek ve güncelle
     */
    public function updateExchangeRates()
    {
        try {
            Log::info('Döviz kurları güncelleniyor...');

            // TCMB XML'ini çek
            $response = Http::timeout(30)->get($this->tcmbUrl);
            
            if (!$response->successful()) {
                throw new \Exception('TCMB API\'sine erişilemedi: ' . $response->status());
            }

            $xml = simplexml_load_string($response->body());
            
            if (!$xml) {
                throw new \Exception('XML parse edilemedi');
            }

            $updatedCount = 0;
            $now = now();

            // Desteklenen para birimleri
            $supportedCurrencies = [
                'USD' => 'US Dollar',
                'EUR' => 'Euro',
                'GBP' => 'British Pound',
                'JPY' => 'Japanese Yen',
                'CHF' => 'Swiss Franc',
                'CAD' => 'Canadian Dollar',
                'AUD' => 'Australian Dollar'
            ];

            foreach ($xml->Currency as $currency) {
                $code = (string) $currency['Kod'];
                
                // Sadece desteklenen para birimlerini işle
                if (!isset($supportedCurrencies[$code])) {
                    continue;
                }

                // Satış kuru (TRY karşılığı)
                $rate = (float) $currency->BanknoteSelling;
                
                if ($rate > 0) {
                    Currency::updateOrCreate(
                        ['code' => $code],
                        [
                            'name' => $supportedCurrencies[$code],
                            'rate' => $rate,
                            'last_updated' => $now
                        ]
                    );
                    
                    $updatedCount++;
                    Log::info("Döviz kuru güncellendi: {$code} = {$rate} TRY");
                }
            }

            // TRY için 1.0000 kur ekle
            Currency::updateOrCreate(
                ['code' => 'TRY'],
                [
                    'name' => 'Turkish Lira',
                    'rate' => 1.0000,
                    'last_updated' => $now
                ]
            );

            Log::info("Döviz kurları başarıyla güncellendi. Güncellenen: {$updatedCount} para birimi");
            
            return [
                'success' => true,
                'updated_count' => $updatedCount,
                'message' => "{$updatedCount} para birimi güncellendi"
            ];

        } catch (\Exception $e) {
            Log::error('Döviz kuru güncelleme hatası: ' . $e->getMessage());
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Döviz kurları güncellenemedi'
            ];
        }
    }

    /**
     * Belirli bir para birimini TRY'ye çevir
     */
    public function convertToTry($amount, $fromCurrency)
    {
        if (strtoupper($fromCurrency) === 'TRY') {
            return $amount;
        }

        $rate = Currency::getRate($fromCurrency);
        return $amount * $rate;
    }

    /**
     * Belirli bir para birimini TRY'den çevir
     */
    public function convertFromTry($amount, $toCurrency)
    {
        if (strtoupper($toCurrency) === 'TRY') {
            return $amount;
        }

        $rate = Currency::getRate($toCurrency);
        return $amount / $rate;
    }

    /**
     * Navbar için döviz kurlarını getir
     */
    public function getNavbarRates()
    {
        return Currency::getRatesFor(['USD', 'EUR', 'GBP']);
    }

    /**
     * Son güncelleme zamanını kontrol et
     */
    public function isRatesStale()
    {
        $lastUpdate = Currency::orderBy('last_updated', 'desc')->value('last_updated');
        
        if (!$lastUpdate) {
            return true; // Hiç güncelleme yok
        }

        // 30 dakikadan eski ise güncelle
        return $lastUpdate->diffInMinutes(now()) > 30;
    }

    /**
     * Manuel kur güncelleme (test için)
     */
    public function updateTestRates()
    {
        $testRates = [
            'USD' => ['name' => 'US Dollar', 'rate' => 48.14],
            'EUR' => ['name' => 'Euro', 'rate' => 45.11],
            'GBP' => ['name' => 'British Pound', 'rate' => 52.85],
            'TRY' => ['name' => 'Turkish Lira', 'rate' => 1.0000]
        ];

        $now = now();
        foreach ($testRates as $code => $data) {
            Currency::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $data['name'],
                    'rate' => $data['rate'],
                    'last_updated' => $now
                ]
            );
        }

        Log::info('Test döviz kurları güncellendi');
        return true;
    }
}
