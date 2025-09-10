<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'kod', 'ad', 'miktar', 'fiyat_sk', 'fiyat_bayi', 'fiyat_ozel',
        'doviz', 'marka', 'kategori', 'ana_grup_kod', 'ana_grup_ad',
        'alt_grup_kod', 'alt_grup_ad', 'ana_resim', 'barkod', 'aciklama',
        'detay', 'desi', 'kdv', 'is_active', 'featured', 'featured_order', 'last_updated',
        'profit_type', 'profit_value', 'profit_enabled'
    ];

    protected $casts = [
        'fiyat_sk' => 'decimal:2',
        'fiyat_bayi' => 'decimal:2',
        'fiyat_ozel' => 'decimal:2',
        'desi' => 'decimal:2',
        'is_active' => 'boolean',
        'featured' => 'boolean',
        'featured_order' => 'integer',
        'last_updated' => 'datetime',
        'profit_type' => 'integer',
        'profit_value' => 'decimal:2',
        'profit_enabled' => 'boolean',
    ];

    /**
     * Ürün resimleri ilişkisi
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Ürün teknik özellikleri ilişkisi
     */
    public function specifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    /**
     * Stokta olan ürünleri getir (2'den fazla stok)
     */
    public function scopeInStock($query)
    {
        return $query->where('miktar', '>=', 2);
    }

    /**
     * Arama scope'u
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('ad', 'LIKE', "%{$search}%")
              ->orWhere('marka', 'LIKE', "%{$search}%")
              ->orWhere('kategori', 'LIKE', "%{$search}%")
              ->orWhere('kod', 'LIKE', "%{$search}%")
              ->orWhere('aciklama', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Aktif ürünleri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Markaya göre filtrele
     */
    public function scopeByBrand($query, $brand)
    {
        return $query->where('marka', $brand);
    }

    /**
     * Kategoriye göre filtrele
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('kategori', $category);
    }

    /**
     * Öne çıkan ürünleri getir
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true)->orderBy('featured_order', 'asc');
    }


    /**
     * En uygun fiyatı getir
     */
    public function getBestPriceAttribute()
    {
        $prices = collect([
            $this->fiyat_ozel,
            $this->fiyat_bayi,
            $this->fiyat_sk
        ])->filter();

        return $prices->min();
    }

    /**
     * En uygun fiyatı TL'ye çevir
     */
    public function getBestPriceInTryAttribute()
    {
        $bestPrice = $this->best_price;
        if (!$bestPrice) {
            return 0;
        }

        if (strtoupper($this->doviz) === 'TRY') {
            return $bestPrice;
        }

        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->convertToTry($bestPrice, $this->doviz);
    }

    /**
     * Kar dahil fiyat hesapla (Kategori kar sistemi ile)
     */
    public function getPriceWithProfitAttribute()
    {
        $basePrice = $this->best_price;
        if (!$basePrice) {
            return $basePrice;
        }

        // Yeni kategori kar sistemi kullan
        return $this->calculatePriceWithCategoryProfit('fiyat_sk');
    }

    /**
     * Kar dahil fiyatı TL'ye çevir
     */
    public function getPriceWithProfitInTryAttribute()
    {
        $priceWithProfit = $this->price_with_profit;
        if (!$priceWithProfit) {
            return 0;
        }

        if (strtoupper($this->doviz) === 'TRY') {
            return $priceWithProfit;
        }

        $currencyService = app(\App\Services\CurrencyService::class);
        return $currencyService->convertToTry($priceWithProfit, $this->doviz);
    }

    /**
     * Fiyat formatı
     */
    public function getFormattedPriceAttribute()
    {
        $price = $this->best_price;
        return $price ? number_format($price, 2) . ' ' . $this->getCurrencySymbol() . ' +KDV' : 'Fiyat Belirtilmemiş';
    }

    /**
     * TL fiyat formatı
     */
    public function getFormattedPriceInTryAttribute()
    {
        $price = $this->best_price_in_try;
        return $price ? number_format($price, 2) . ' ₺ +KDV' : 'Fiyat Belirtilmemiş';
    }

    /**
     * Kar dahil fiyat formatı
     */
    public function getFormattedPriceWithProfitAttribute()
    {
        $price = $this->price_with_profit;
        return $price ? number_format($price, 2) . ' ' . $this->getCurrencySymbol() . ' +KDV' : 'Fiyat Belirtilmemiş';
    }

    /**
     * Kar dahil TL fiyat formatı
     */
    public function getFormattedPriceWithProfitInTryAttribute()
    {
        $price = $this->price_with_profit_in_try;
        return $price ? number_format($price, 2) . ' ₺ +KDV' : 'Fiyat Belirtilmemiş';
    }

    /**
     * Para birimi sembolü
     */
    public function getCurrencySymbol()
    {
        return match(strtoupper($this->doviz)) {
            'TRY', 'TL' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->doviz
        };
    }

    /**
     * Para birimi sembolü (static method)
     */
    public static function getCurrencySymbolFor($currency)
    {
        return match(strtoupper($currency)) {
            'TRY', 'TL' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $currency
        };
    }

    /**
     * Bu ürünü favorilere ekleyen kullanıcılar
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'product_kod', 'user_id', 'kod', 'id')
            ->withTimestamps();
    }

    /**
     * Belirli bir kullanıcının bu ürünü favorilere ekleyip eklemediğini kontrol et
     */
    public function isFavoritedBy($userId)
    {
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Fiyat değişikliği geçmişi ilişkisi
     */
    public function priceHistory()
    {
        return $this->hasMany(\App\Models\ProductPriceHistory::class, 'product_kod', 'kod');
    }

    /**
     * Kategori kar ayarını getir
     */
    public function getCategoryProfit()
    {
        if (empty($this->kategori)) {
            return null;
        }
        
        return CategoryProfit::getProfitForCategory($this->kategori);
    }

    /**
     * Kategori kar dahil fiyat hesapla
     */
    public function calculatePriceWithCategoryProfit($priceType = 'fiyat_sk')
    {
        $basePrice = $this->$priceType;
        
        if (!$basePrice || $basePrice <= 0) {
            return $basePrice;
        }

        // Önce ürün özel kar ayarını kontrol et
        if ($this->profit_enabled && $this->profit_type && $this->profit_value) {
            return $this->calculateProductProfit($basePrice);
        }

        // Sonra kategori kar ayarını kontrol et
        $categoryProfit = $this->getCategoryProfit();
        if ($categoryProfit) {
            return $categoryProfit->calculateProfit($basePrice);
        }

        // Son olarak genel kar ayarını kontrol et
        $globalSettings = GlobalProfitSetting::getSettings();
        if ($globalSettings->is_enabled && $globalSettings->profit_type && $globalSettings->profit_value) {
            return $this->calculateGlobalProfit($basePrice, $globalSettings);
        }

        return $basePrice;
    }

    /**
     * Ürün özel kar hesaplama
     */
    private function calculateProductProfit($basePrice)
    {
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
     * Genel kar hesaplama
     */
    private function calculateGlobalProfit($basePrice, $settings)
    {
        switch ($settings->profit_type) {
            case 1: // Yüzde kar
                return $basePrice * (1 + ($settings->profit_value / 100));
            case 2: // Sabit kar
                return $basePrice + $settings->profit_value;
            default:
                return $basePrice;
        }
    }

    /**
     * Kategori kar dahil SK fiyatı
     */
    public function getPriceSkWithCategoryProfitAttribute()
    {
        return $this->calculatePriceWithCategoryProfit('fiyat_sk');
    }

    /**
     * Kategori kar dahil Bayi fiyatı
     */
    public function getPriceBayiWithCategoryProfitAttribute()
    {
        return $this->calculatePriceWithCategoryProfit('fiyat_bayi');
    }

    /**
     * Kategori kar dahil Özel fiyatı
     */
    public function getPriceOzelWithCategoryProfitAttribute()
    {
        return $this->calculatePriceWithCategoryProfit('fiyat_ozel');
    }

    /**
     * Ürün güncelleme işleminde fiyat değişikliğini kontrol et
     */
    public function updateWithPriceCheck(array $data)
    {
        // Mevcut fiyatları sakla
        $oldPrices = [
            'fiyat_sk' => $this->fiyat_sk,
            'fiyat_bayi' => $this->fiyat_bayi,
            'fiyat_ozel' => $this->fiyat_ozel,
        ];

        // Ürünü güncelle
        $this->update($data);

        // Yeni fiyatları al
        $newPrices = [
            'fiyat_sk' => $this->fresh()->fiyat_sk,
            'fiyat_bayi' => $this->fresh()->fiyat_bayi,
            'fiyat_ozel' => $this->fresh()->fiyat_ozel,
        ];

        // Fiyat değişikliği var mı kontrol et
        $priceChanged = false;
        foreach (['fiyat_sk', 'fiyat_bayi', 'fiyat_ozel'] as $priceField) {
            if (($oldPrices[$priceField] ?? 0) != ($newPrices[$priceField] ?? 0)) {
                $priceChanged = true;
                break;
            }
        }

        // Eğer fiyat değiştiyse, bildirim servisini çağır
        if ($priceChanged) {
            try {
                $priceDropService = app(\App\Services\PriceDropNotificationService::class);
                $priceDropService->checkAndNotifyPriceDrop($this, $oldPrices, $newPrices);
            } catch (\Exception $e) {
                \Log::error('Fiyat değişikliği bildirimi hatası: ' . $e->getMessage(), [
                    'product_kod' => $this->kod,
                    'old_prices' => $oldPrices,
                    'new_prices' => $newPrices,
                ]);
            }
        }

        return $this;
    }
}
