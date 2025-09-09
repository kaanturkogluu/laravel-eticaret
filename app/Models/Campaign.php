<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title', 'description', 'image_url', 'link_url', 'type',
        'is_active', 'sort_order', 'start_date', 'end_date',
        'discount_type', 'discount_value', 'minimum_amount', 'maximum_discount',
        'applicable_products', 'applicable_categories', 'excluded_products', 'excluded_categories'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'excluded_products' => 'array',
        'excluded_categories' => 'array',
    ];

    /**
     * Aktif kampanyaları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Banner tipindeki kampanyaları getir
     */
    public function scopeBanners($query)
    {
        return $query->where('type', 'banner');
    }

    /**
     * Kampanya tipindeki kampanyaları getir
     */
    public function scopeCampaigns($query)
    {
        return $query->where('type', 'campaign');
    }

    /**
     * Promosyon tipindeki kampanyaları getir
     */
    public function scopePromotions($query)
    {
        return $query->where('type', 'promotion');
    }

    /**
     * Sıralama
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }

    /**
     * İndirimli kampanyaları getir
     */
    public function scopeWithDiscount($query)
    {
        return $query->whereNotNull('discount_type')
                    ->whereNotNull('discount_value')
                    ->where('discount_value', '>', 0);
    }

    /**
     * Kampanya geçerli mi kontrol et
     */
    public function isValid(): bool
    {
        // Aktif değilse
        if (!$this->is_active) {
            return false;
        }

        // Başlangıç tarihi kontrolü
        if ($this->start_date && $this->start_date > now()) {
            return false;
        }

        // Bitiş tarihi kontrolü
        if ($this->end_date && $this->end_date < now()) {
            return false;
        }

        return true;
    }

    /**
     * İndirim tutarını hesapla
     */
    public function calculateDiscount($amount): float
    {
        if (!$this->discount_type || !$this->discount_value) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($amount * $this->discount_value) / 100;
            
            // Maksimum indirim kontrolü
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
        } else {
            $discount = $this->discount_value;
        }

        // İndirim tutarı sepet tutarından fazla olamaz
        return min($discount, $amount);
    }

    /**
     * Kampanya uygulanabilir mi kontrol et
     */
    public function canBeAppliedTo($amount, $products = []): array
    {
        $result = [
            'valid' => false,
            'message' => '',
            'discount' => 0
        ];

        // Temel geçerlilik kontrolü
        if (!$this->isValid()) {
            $result['message'] = 'Bu kampanya geçersiz veya süresi dolmuş.';
            return $result;
        }

        // Minimum tutar kontrolü
        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            $result['message'] = "Bu kampanyayı kullanabilmek için minimum {$this->minimum_amount} TL tutarında alışveriş yapmalısınız.";
            return $result;
        }

        // Ürün uygunluk kontrolü
        if ($products && count($products) > 0) {
            if (!$this->isApplicableToProducts($products)) {
                $result['message'] = 'Bu kampanya sepetinizdeki ürünlere uygulanamaz.';
                return $result;
            }
        }

        // İndirim hesapla
        $discount = $this->calculateDiscount($amount);
        
        if ($discount <= 0) {
            $result['message'] = 'Bu kampanya için geçerli bir indirim hesaplanamadı.';
            return $result;
        }

        $result['valid'] = true;
        $result['discount'] = $discount;
        $result['message'] = "Kampanya başarıyla uygulandı. İndirim: " . number_format($discount, 2) . " TL";

        return $result;
    }

    /**
     * Ürünlere uygulanabilir mi kontrol et
     */
    public function isApplicableToProducts($products): bool
    {
        // Eğer uygulanabilir ürünler belirtilmişse
        if ($this->applicable_products && count($this->applicable_products) > 0) {
            $productCodes = collect($products)->pluck('kod')->toArray();
            $applicableCodes = $this->applicable_products;
            
            // En az bir ürün uygulanabilir listede olmalı
            if (count(array_intersect($productCodes, $applicableCodes)) === 0) {
                return false;
            }
        }

        // Eğer hariç tutulan ürünler belirtilmişse
        if ($this->excluded_products && count($this->excluded_products) > 0) {
            $productCodes = collect($products)->pluck('kod')->toArray();
            $excludedCodes = $this->excluded_products;
            
            // Hiçbir ürün hariç tutulan listede olmamalı
            if (count(array_intersect($productCodes, $excludedCodes)) > 0) {
                return false;
            }
        }

        // Eğer uygulanabilir kategoriler belirtilmişse
        if ($this->applicable_categories && count($this->applicable_categories) > 0) {
            $productCategories = collect($products)->pluck('kategori')->filter()->unique()->toArray();
            $applicableCategories = $this->applicable_categories;
            
            // En az bir kategori uygulanabilir listede olmalı
            if (count(array_intersect($productCategories, $applicableCategories)) === 0) {
                return false;
            }
        }

        // Eğer hariç tutulan kategoriler belirtilmişse
        if ($this->excluded_categories && count($this->excluded_categories) > 0) {
            $productCategories = collect($products)->pluck('kategori')->filter()->unique()->toArray();
            $excludedCategories = $this->excluded_categories;
            
            // Hiçbir kategori hariç tutulan listede olmamalı
            if (count(array_intersect($productCategories, $excludedCategories)) > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * İndirim tipi etiketi
     */
    public function getDiscountTypeLabelAttribute(): string
    {
        if (!$this->discount_type) {
            return 'İndirim Yok';
        }
        return $this->discount_type === 'percentage' ? 'Yüzde' : 'Sabit Tutar';
    }

    /**
     * Formatlanmış indirim değeri
     */
    public function getFormattedDiscountValueAttribute(): string
    {
        if (!$this->discount_value) {
            return '0';
        }
        
        if ($this->discount_type === 'percentage') {
            return '%' . $this->discount_value;
        }
        return number_format($this->discount_value, 2) . ' TL';
    }
}