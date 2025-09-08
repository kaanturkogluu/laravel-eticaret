<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'status', 'subtotal', 'subtotal_tl', 'shipping_cost', 'shipping_cost_tl', 'total', 'total_tl', 'currency',
        'customer_name', 'customer_email', 'customer_phone',
        'shipping_address', 'shipping_city', 'shipping_district', 'shipping_postal_code',
        'billing_address', 'billing_city', 'billing_district', 'billing_postal_code',
        'payment_status', 'payment_method', 'payment_reference', 'paid_at',
        'shipping_method', 'tracking_number', 'shipped_at', 'delivered_at',
        'cargo_company_id', 'cargo_tracking_number', 'cargo_created_at', 'cargo_picked_up_at', 'cargo_delivered_at',
        'notes', 'admin_notes', 'coupon_id', 'coupon_code', 'discount_amount', 'discount_amount_tl'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'subtotal_tl' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'shipping_cost_tl' => 'decimal:2',
        'total' => 'decimal:2',
        'total_tl' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'discount_amount_tl' => 'decimal:2',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cargo_created_at' => 'datetime',
        'cargo_picked_up_at' => 'datetime',
        'cargo_delivered_at' => 'datetime',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sipariş kalemleri ilişkisi
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Payment transactions ilişkisi
     */
    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /**
     * Son payment transaction
     */
    public function latestPaymentTransaction()
    {
        return $this->hasOne(PaymentTransaction::class)->latest();
    }

    /**
     * Kargo şirketi ilişkisi
     */
    public function cargoCompany(): BelongsTo
    {
        return $this->belongsTo(CargoCompany::class);
    }

    /**
     * Kupon ilişkisi
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Kargo takip kayıtları ilişkisi
     */
    public function cargoTrackings(): HasMany
    {
        return $this->hasMany(CargoTracking::class);
    }

    /**
     * Son kargo takip kaydı
     */
    public function latestCargoTracking()
    {
        return $this->hasOne(CargoTracking::class)->latest('event_date');
    }

    /**
     * Sipariş numarası oluştur
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Sipariş durumu etiketleri
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Beklemede',
            'processing' => 'İşleniyor',
            'shipped' => 'Kargoya Verildi',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Ödeme durumu etiketleri
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'Beklemede',
            'paid' => 'Ödendi',
            'failed' => 'Başarısız',
            'refunded' => 'İade Edildi',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Sipariş durumuna göre renk
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'shipped' => 'primary',
            'delivered' => 'success',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Ödeme durumuna göre renk
     */
    public function getPaymentStatusColorAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'warning',
            'paid' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            default => 'secondary'
        };
    }

    /**
     * Siparişi iptal et
     */
    public function cancel(): bool
    {
        if (in_array($this->status, ['pending', 'processing'])) {
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }

    /**
     * Siparişi teslim edildi olarak işaretle
     */
    public function markAsDelivered(): bool
    {
        if ($this->status === 'shipped') {
            $this->update([
                'status' => 'delivered',
                'delivered_at' => now()
            ]);
            return true;
        }
        return false;
    }

    /**
     * Siparişi kargoya verildi olarak işaretle
     */
    public function markAsShipped(string $trackingNumber = null): bool
    {
        if (in_array($this->status, ['processing', 'pending'])) {
            $this->update([
                'status' => 'shipped',
                'shipped_at' => now(),
                'tracking_number' => $trackingNumber
            ]);
            return true;
        }
        return false;
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
     * Formatlanmış ara toplam
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2) . ' ' . $this->getCurrencySymbol();
    }

    /**
     * Formatlanmış kargo ücreti
     */
    public function getFormattedShippingCostAttribute()
    {
        if ($this->shipping_cost > 0) {
            return number_format($this->shipping_cost, 2) . ' ' . $this->getCurrencySymbol();
        }
        return 'Ücretsiz';
    }

    /**
     * Formatlanmış toplam
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . ' ' . $this->getCurrencySymbol();
    }

    /**
     * TL karşılığı ara toplam
     */
    public function getSubtotalTlAttribute()
    {
        if (isset($this->attributes['subtotal_tl']) && $this->attributes['subtotal_tl']) {
            return $this->attributes['subtotal_tl'];
        }
        
        // Eğer TL karşılığı kaydedilmemişse, mevcut kurlarla hesapla
        if ($this->currency === 'TRY') {
            return $this->subtotal;
        }
        
        $rate = \App\Models\Currency::getRate($this->currency);
        return $this->subtotal * $rate;
    }

    /**
     * TL karşılığı kargo ücreti
     */
    public function getShippingCostTlAttribute()
    {
        if (isset($this->attributes['shipping_cost_tl']) && $this->attributes['shipping_cost_tl']) {
            return $this->attributes['shipping_cost_tl'];
        }
        
        if ($this->currency === 'TRY') {
            return $this->shipping_cost;
        }
        
        $rate = \App\Models\Currency::getRate($this->currency);
        return $this->shipping_cost * $rate;
    }

    /**
     * TL karşılığı toplam
     */
    public function getTotalTlAttribute()
    {
        if (isset($this->attributes['total_tl']) && $this->attributes['total_tl']) {
            return $this->attributes['total_tl'];
        }
        
        if ($this->currency === 'TRY') {
            return $this->total;
        }
        
        $rate = \App\Models\Currency::getRate($this->currency);
        return $this->total * $rate;
    }

    /**
     * Formatlanmış TL karşılığı ara toplam
     */
    public function getFormattedSubtotalTlAttribute()
    {
        return number_format($this->getSubtotalTlAttribute(), 2) . ' ₺';
    }

    /**
     * Formatlanmış TL karşılığı kargo ücreti
     */
    public function getFormattedShippingCostTlAttribute()
    {
        $shippingCostTl = $this->getShippingCostTlAttribute();
        if ($shippingCostTl > 0) {
            return number_format($shippingCostTl, 2) . ' ₺';
        }
        return 'Ücretsiz';
    }

    /**
     * Formatlanmış TL karşılığı toplam
     */
    public function getFormattedTotalTlAttribute()
    {
        return number_format($this->getTotalTlAttribute(), 2) . ' ₺';
    }

    /**
     * Formatlanmış indirim tutarı
     */
    public function getFormattedDiscountAmountAttribute()
    {
        if ($this->discount_amount > 0) {
            return number_format($this->discount_amount, 2) . ' ' . $this->getCurrencySymbol();
        }
        return '0.00 ' . $this->getCurrencySymbol();
    }

    /**
     * Formatlanmış TL karşılığı indirim tutarı
     */
    public function getFormattedDiscountAmountTlAttribute()
    {
        if ($this->discount_amount_tl > 0) {
            return number_format($this->discount_amount_tl, 2) . ' ₺';
        }
        return '0.00 ₺';
    }

    /**
     * Kupon kullanıldı mı?
     */
    public function hasCoupon(): bool
    {
        return !is_null($this->coupon_id) && $this->discount_amount > 0;
    }

    /**
     * Kupon bilgilerini getir
     */
    public function getCouponInfo(): ?array
    {
        if (!$this->hasCoupon()) {
            return null;
        }

        return [
            'code' => $this->coupon_code,
            'discount_amount' => $this->discount_amount,
            'discount_amount_tl' => $this->discount_amount_tl,
            'formatted_discount' => $this->formatted_discount_amount,
            'formatted_discount_tl' => $this->formatted_discount_amount_tl,
        ];
    }
}
