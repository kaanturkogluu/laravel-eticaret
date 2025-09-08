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
        'detay', 'desi', 'kdv', 'is_active', 'last_updated'
    ];

    protected $casts = [
        'fiyat_sk' => 'decimal:2',
        'fiyat_bayi' => 'decimal:2',
        'fiyat_ozel' => 'decimal:2',
        'desi' => 'decimal:2',
        'is_active' => 'boolean',
        'last_updated' => 'datetime',
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
     * Fiyat formatı
     */
    public function getFormattedPriceAttribute()
    {
        $price = $this->best_price;
        return $price ? number_format($price, 2) . ' ' . $this->getCurrencySymbol() : 'Fiyat Belirtilmemiş';
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
}
