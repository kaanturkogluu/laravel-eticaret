<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargoCompany extends Model
{
    protected $fillable = [
        'name', 'code', 'api_url', 'api_key', 'api_secret',
        'tracking_url', 'is_active', 'sort_order', 'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Kargo takip kayıtları ilişkisi
     */
    public function cargoTrackings(): HasMany
    {
        return $this->hasMany(CargoTracking::class);
    }

    /**
     * Siparişler ilişkisi
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'cargo_company_id');
    }

    /**
     * Aktif kargo şirketlerini getir
     */
    public static function getActiveCompanies()
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
    }

    /**
     * Kargo şirketi kodu ile bul
     */
    public static function findByCode(string $code)
    {
        return static::where('code', $code)->where('is_active', true)->first();
    }

    /**
     * Takip URL'si oluştur
     */
    public function getTrackingUrl(string $trackingNumber): string
    {
        if ($this->tracking_url) {
            return str_replace('{tracking_number}', $trackingNumber, $this->tracking_url);
        }
        
        return '#';
    }

    /**
     * API ile takip bilgisi al
     */
    public function getTrackingInfo(string $trackingNumber): array
    {
        // Bu method kargo şirketinin API'sine göre implement edilecek
        // Şimdilik boş array döndürüyoruz
        return [
            'status' => 'unknown',
            'events' => [],
            'last_update' => null
        ];
    }
}
