<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id', 'user_id', 'order_id', 'session_id', 
        'discount_amount', 'order_total', 'ip_address'
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'order_total' => 'decimal:2',
    ];

    /**
     * Kupon ilişkisi
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sipariş ilişkisi
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Formatlanmış indirim tutarı
     */
    public function getFormattedDiscountAmountAttribute(): string
    {
        return number_format($this->discount_amount, 2) . ' TL';
    }

    /**
     * Formatlanmış sipariş tutarı
     */
    public function getFormattedOrderTotalAttribute(): string
    {
        return number_format($this->order_total, 2) . ' TL';
    }
}
