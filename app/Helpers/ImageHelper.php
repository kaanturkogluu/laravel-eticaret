<?php

namespace App\Helpers;

use App\Services\ImageOptimizationService;

class ImageHelper
{
    /**
     * Generate responsive image HTML with WebP support
     */
    public static function responsiveImage(string $imagePath, string $alt = '', array $attributes = []): string
    {
        $imageOptimizationService = app(ImageOptimizationService::class);
        return $imageOptimizationService->generateResponsiveImageHtml($imagePath, $alt, $attributes);
    }

    /**
     * Get optimized image URL for specific size
     */
    public static function optimizedUrl(string $imagePath, string $size = 'large'): string
    {
        $imageOptimizationService = app(ImageOptimizationService::class);
        return $imageOptimizationService->getOptimizedImageUrl($imagePath, $size);
    }

    /**
     * Generate lazy loading image with placeholder
     */
    public static function lazyImage(string $imagePath, string $alt = '', array $attributes = []): string
    {
        $defaultAttributes = [
            'loading' => 'lazy',
            'class' => 'lazy-image'
        ];
        
        $attributes = array_merge($defaultAttributes, $attributes);
        
        return self::responsiveImage($imagePath, $alt, $attributes);
    }

    /**
     * Generate product image with fallback
     */
    public static function productImage(string $imagePath, string $alt = '', array $attributes = []): string
    {
        if (empty($imagePath)) {
            $imagePath = '/images/no-product-image.svg';
        }
        
        $defaultAttributes = [
            'class' => 'product-image',
            'loading' => 'lazy'
        ];
        
        $attributes = array_merge($defaultAttributes, $attributes);
        
        return self::responsiveImage($imagePath, $alt, $attributes);
    }
}
