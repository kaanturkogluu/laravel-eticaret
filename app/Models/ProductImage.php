<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'urun_kodu', 'resim_url', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Ürün ilişkisi
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Resim URL accessor - resim_url alanını image_url olarak erişilebilir yapar
     */
    public function getImageUrlAttribute()
    {
        return $this->resim_url;
    }

    /**
     * Optimized image URL for different sizes
     */
    public function getOptimizedUrl($size = 'large')
    {
        $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
        return $imageOptimizationService->getOptimizedImageUrl($this->resim_url, $size);
    }

    /**
     * Generate responsive image HTML
     */
    public function getResponsiveImageHtml($alt = '', $attributes = [])
    {
        $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
        return $imageOptimizationService->generateResponsiveImageHtml($this->resim_url, $alt, $attributes);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute()
    {
        return $this->getOptimizedUrl('thumbnail');
    }

    /**
     * Get medium size URL
     */
    public function getMediumUrlAttribute()
    {
        return $this->getOptimizedUrl('medium');
    }

    /**
     * Get large size URL
     */
    public function getLargeUrlAttribute()
    {
        return $this->getOptimizedUrl('large');
    }
}
