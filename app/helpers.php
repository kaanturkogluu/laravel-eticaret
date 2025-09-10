<?php

if (!function_exists('responsive_image')) {
    function responsive_image(string $imagePath, string $alt = '', array $attributes = []): string
    {
        return \App\Helpers\ImageHelper::responsiveImage($imagePath, $alt, $attributes);
    }
}

if (!function_exists('optimized_image_url')) {
    function optimized_image_url(string $imagePath, string $size = 'large'): string
    {
        return \App\Helpers\ImageHelper::optimizedUrl($imagePath, $size);
    }
}

if (!function_exists('product_image')) {
    function product_image(string $imagePath, string $alt = '', array $attributes = []): string
    {
        return \App\Helpers\ImageHelper::productImage($imagePath, $alt, $attributes);
    }
}
