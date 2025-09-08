<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'session_id',
        'coupon_id',
        'discount_amount',
        'total_after_discount'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'discount_amount' => 'decimal:2',
        'total_after_discount' => 'decimal:2',
    ];

    /**
     * Kullanıcı ilişkisi
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ürün ilişkisi
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Kupon ilişkisi
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Toplam fiyat hesapla
     */
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->product->best_price;
    }

    /**
     * Kullanıcı sepetini getir
     */
    public static function getUserCart($userId = null, $sessionId = null)
    {
        $query = static::with('product.images');
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->get();
    }

    /**
     * Sepet toplam tutarı
     */
    public static function getCartTotal($userId = null, $sessionId = null)
    {
        $cartItems = static::getUserCart($userId, $sessionId);
        
        return $cartItems->sum(function ($item) {
            return $item->total_price;
        });
    }

    /**
     * Sepet ürün sayısı
     */
    public static function getCartCount($userId = null, $sessionId = null)
    {
        $query = static::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        } elseif ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        return $query->sum('quantity');
    }

    /**
     * Formatlanmış toplam fiyat
     */
    public function getFormattedTotalPriceAttribute()
    {
        $symbol = $this->product->getCurrencySymbol();
        return number_format($this->total_price, 2) . ' ' . $symbol;
    }

    /**
     * Formatlanmış birim fiyat
     */
    public function getFormattedUnitPriceAttribute()
    {
        $symbol = $this->product->getCurrencySymbol();
        return number_format($this->product->best_price, 2) . ' ' . $symbol;
    }

    /**
     * TL karşılığı toplam fiyat
     */
    public function getTotalPriceInTryAttribute()
    {
        if ($this->product->doviz === 'TRY') {
            return $this->total_price;
        }
        
        // Diğer para birimleri için döviz kuru ile çarp
        $rate = \App\Models\Currency::getRate($this->product->doviz);
        return $this->total_price * $rate;
    }

    /**
     * Formatlanmış TL karşılığı toplam fiyat
     */
    public function getFormattedTotalPriceInTryAttribute()
    {
        return number_format($this->total_price_in_try, 2) . ' ₺';
    }

    /**
     * Sepet toplamını TL karşılığında hesapla
     */
    public static function getCartTotalInTry($userId = null, $sessionId = null)
    {
        $cartItems = static::getUserCart($userId, $sessionId);
        
        return $cartItems->sum(function ($item) {
            return $item->total_price_in_try;
        });
    }

    /**
     * Kupon uygulanmış sepet toplamı
     */
    public static function getCartTotalWithDiscount($userId = null, $sessionId = null)
    {
        $total = static::getCartTotalInTry($userId, $sessionId);
        $appliedCoupon = session('applied_coupon');
        
        if ($appliedCoupon && isset($appliedCoupon['discount_amount'])) {
            $total -= $appliedCoupon['discount_amount'];
        }
        
        return max(0, $total);
    }

    /**
     * Uygulanan kupon bilgilerini getir
     */
    public static function getAppliedCoupon()
    {
        return session('applied_coupon');
    }

    /**
     * Kupon uygula
     */
    public static function applyCoupon($coupon, $userId = null, $sessionId = null)
    {
        $cartTotal = static::getCartTotalInTry($userId, $sessionId);
        $validation = $coupon->canBeUsedFor($cartTotal, $userId, $sessionId);
        
        if ($validation['valid']) {
            session(['applied_coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_amount' => $validation['discount'],
                'free_shipping' => $coupon->free_shipping
            ]]);
            
            return $validation;
        }
        
        return $validation;
    }

    /**
     * Kupon kaldır
     */
    public static function removeCoupon()
    {
        session()->forget('applied_coupon');
    }

    /**
     * İndirim tutarını getir
     */
    public static function getDiscountAmount()
    {
        $appliedCoupon = static::getAppliedCoupon();
        return $appliedCoupon ? $appliedCoupon['discount_amount'] : 0;
    }

    /**
     * Ücretsiz kargo var mı kontrol et
     */
    public static function hasFreeShipping()
    {
        $appliedCoupon = static::getAppliedCoupon();
        return $appliedCoupon ? $appliedCoupon['free_shipping'] : false;
    }
}