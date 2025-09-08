<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CargoTracking extends Model
{
    protected $fillable = [
        'order_id', 'cargo_company_id', 'tracking_number', 'status',
        'description', 'location', 'event_date', 'is_delivered'
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'is_delivered' => 'boolean',
    ];

    /**
     * Sipariş ilişkisi
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Kargo şirketi ilişkisi
     */
    public function cargoCompany(): BelongsTo
    {
        return $this->belongsTo(CargoCompany::class);
    }

    /**
     * Durum etiketleri
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'created' => 'Kargo Oluşturuldu',
            'picked_up' => 'Kargo Alındı',
            'in_transit' => 'Yolda',
            'out_for_delivery' => 'Dağıtımda',
            'delivered' => 'Teslim Edildi',
            'exception' => 'Sorun Var',
            'returned' => 'İade Edildi',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Durum renkleri
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'created' => 'info',
            'picked_up' => 'primary',
            'in_transit' => 'warning',
            'out_for_delivery' => 'warning',
            'delivered' => 'success',
            'exception' => 'danger',
            'returned' => 'secondary',
            default => 'secondary'
        };
    }

    /**
     * Formatlanmış tarih
     */
    public function getFormattedEventDateAttribute(): string
    {
        return $this->event_date ? $this->event_date->format('d.m.Y H:i') : '-';
    }

    /**
     * Takip numarası ile bul
     */
    public static function findByTrackingNumber(string $trackingNumber)
    {
        return static::where('tracking_number', $trackingNumber)->first();
    }

    /**
     * Sipariş için son takip kaydı
     */
    public static function getLatestForOrder(int $orderId)
    {
        return static::where('order_id', $orderId)
            ->orderBy('event_date', 'desc')
            ->first();
    }

    /**
     * Sipariş için tüm takip geçmişi
     */
    public static function getHistoryForOrder(int $orderId)
    {
        return static::where('order_id', $orderId)
            ->orderBy('event_date', 'asc')
            ->get();
    }
}
