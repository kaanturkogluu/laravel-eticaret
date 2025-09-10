<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalProfitSetting extends Model
{
    protected $fillable = [
        'is_enabled', 'profit_type', 'profit_value'
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'profit_type' => 'integer',
        'profit_value' => 'decimal:2',
    ];

    /**
     * Genel kar ayarlarını getir (tek kayıt)
     */
    public static function getSettings()
    {
        return static::first() ?? static::create([
            'is_enabled' => false,
            'profit_type' => 1,
            'profit_value' => 0
        ]);
    }

    /**
     * Genel kar ayarlarını güncelle
     */
    public static function updateSettings($data)
    {
        $settings = static::getSettings();
        $settings->update($data);
        return $settings;
    }

    /**
     * Genel kar hesapla
     */
    public function calculateProfit($basePrice)
    {
        if (!$this->is_enabled) {
            return $basePrice;
        }

        switch ($this->profit_type) {
            case 1: // Yüzde kar
                return $basePrice * (1 + ($this->profit_value / 100));
            case 2: // Sabit kar
                return $basePrice + $this->profit_value;
            default:
                return $basePrice;
        }
    }

    /**
     * Genel kar aktif mi?
     */
    public static function isEnabled()
    {
        return static::getSettings()->is_enabled;
    }

    /**
     * Genel kar ayarlarını al
     */
    public static function getProfitSettings()
    {
        return static::getSettings();
    }
}
