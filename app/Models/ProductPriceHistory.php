<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    protected $table = 'product_price_history';

    protected $fillable = [
        'product_kod',
        'old_price_sk',
        'old_price_bayi',
        'old_price_ozel',
        'new_price_sk',
        'new_price_bayi',
        'new_price_ozel',
        'currency',
        'price_difference',
        'discount_percentage',
        'is_discount',
        'changed_at',
    ];

    protected $casts = [
        'old_price_sk' => 'decimal:2',
        'old_price_bayi' => 'decimal:2',
        'old_price_ozel' => 'decimal:2',
        'new_price_sk' => 'decimal:2',
        'new_price_bayi' => 'decimal:2',
        'new_price_ozel' => 'decimal:2',
        'price_difference' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_discount' => 'boolean',
        'changed_at' => 'datetime',
    ];

    /**
     * İlgili ürün
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_kod', 'kod');
    }

    /**
     * En uygun eski fiyatı getir
     */
    public function getOldBestPriceAttribute()
    {
        $prices = collect([
            $this->old_price_ozel,
            $this->old_price_bayi,
            $this->old_price_sk
        ])->filter();

        return $prices->min();
    }

    /**
     * En uygun yeni fiyatı getir
     */
    public function getNewBestPriceAttribute()
    {
        $prices = collect([
            $this->new_price_ozel,
            $this->new_price_bayi,
            $this->new_price_sk
        ])->filter();

        return $prices->min();
    }

    /**
     * Fiyat değişikliği kaydet
     */
    public static function recordPriceChange($productKod, $oldPrices, $newPrices, $currency = 'TRY')
    {
        $oldBestPrice = collect([
            $oldPrices['fiyat_ozel'] ?? null,
            $oldPrices['fiyat_bayi'] ?? null,
            $oldPrices['fiyat_sk'] ?? null
        ])->filter()->min();

        $newBestPrice = collect([
            $newPrices['fiyat_ozel'] ?? null,
            $newPrices['fiyat_bayi'] ?? null,
            $newPrices['fiyat_sk'] ?? null
        ])->filter()->min();

        $priceDifference = null;
        $discountPercentage = null;
        $isDiscount = false;

        if ($oldBestPrice && $newBestPrice) {
            $priceDifference = $newBestPrice - $oldBestPrice;
            if ($priceDifference < 0) {
                $isDiscount = true;
                $discountPercentage = abs(($priceDifference / $oldBestPrice) * 100);
            }
        }

        return self::create([
            'product_kod' => $productKod,
            'old_price_sk' => $oldPrices['fiyat_sk'] ?? null,
            'old_price_bayi' => $oldPrices['fiyat_bayi'] ?? null,
            'old_price_ozel' => $oldPrices['fiyat_ozel'] ?? null,
            'new_price_sk' => $newPrices['fiyat_sk'] ?? null,
            'new_price_bayi' => $newPrices['fiyat_bayi'] ?? null,
            'new_price_ozel' => $newPrices['fiyat_ozel'] ?? null,
            'currency' => $currency,
            'price_difference' => $priceDifference,
            'discount_percentage' => $discountPercentage,
            'is_discount' => $isDiscount,
            'changed_at' => now(),
        ]);
    }

    /**
     * Son indirimleri getir
     */
    public static function getRecentDiscounts($limit = 10)
    {
        return self::where('is_discount', true)
            ->with('product')
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Belirli bir ürünün son fiyat değişikliklerini getir
     */
    public static function getProductPriceHistory($productKod, $limit = 5)
    {
        return self::where('product_kod', $productKod)
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
