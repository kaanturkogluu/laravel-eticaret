<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'product_id', 'product_code', 'product_name',
        'quantity', 'unit_price', 'unit_price_tl', 'total_price', 'total_price_tl', 'currency'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'unit_price_tl' => 'decimal:2',
        'total_price' => 'decimal:2',
        'total_price_tl' => 'decimal:2',
    ];

    /**
     * Sipariş ilişkisi
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Ürün ilişkisi
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Toplam fiyat hesapla
     */
    public function calculateTotalPrice(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Toplam fiyatı güncelle
     */
    public function updateTotalPrice(): void
    {
        $this->total_price = $this->calculateTotalPrice();
        $this->save();
    }

    /**
     * Para birimi sembolü
     */
    public function getCurrencySymbol()
    {
        return match(strtoupper($this->currency)) {
            'TRY', 'TL' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency
        };
    }

    /**
     * Formatlanmış birim fiyat
     */
    public function getFormattedUnitPriceAttribute()
    {
        return number_format($this->unit_price, 2) . ' ' . $this->getCurrencySymbol();
    }

    /**
     * Formatlanmış toplam fiyat
     */
    public function getFormattedTotalPriceAttribute()
    {
        return number_format($this->total_price, 2) . ' ' . $this->getCurrencySymbol();
    }

    /**
     * TL karşılığı birim fiyat
     */
    public function getUnitPriceTlAttribute()
    {
        if (isset($this->attributes['unit_price_tl']) && $this->attributes['unit_price_tl']) {
            return $this->attributes['unit_price_tl'];
        }
        
        if ($this->currency === 'TRY') {
            return $this->unit_price;
        }
        
        $rate = \App\Models\Currency::getRate($this->currency);
        return $this->unit_price * $rate;
    }

    /**
     * TL karşılığı toplam fiyat
     */
    public function getTotalPriceTlAttribute()
    {
        if (isset($this->attributes['total_price_tl']) && $this->attributes['total_price_tl']) {
            return $this->attributes['total_price_tl'];
        }
        
        if ($this->currency === 'TRY') {
            return $this->total_price;
        }
        
        $rate = \App\Models\Currency::getRate($this->currency);
        return $this->total_price * $rate;
    }

    /**
     * Formatlanmış TL karşılığı birim fiyat
     */
    public function getFormattedUnitPriceTlAttribute()
    {
        return number_format($this->getUnitPriceTlAttribute(), 2) . ' ₺';
    }

    /**
     * Formatlanmış TL karşılığı toplam fiyat
     */
    public function getFormattedTotalPriceTlAttribute()
    {
        return number_format($this->getTotalPriceTlAttribute(), 2) . ' ₺';
    }
}
