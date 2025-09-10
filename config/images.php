<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Image Optimization Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for image optimization
    | including quality settings, dimensions, and format preferences.
    |
    */

    'quality' => [
        'thumbnail' => 80,
        'medium' => 85,
        'large' => 90,
    ],

    'dimensions' => [
        'thumbnail' => [
            'width' => 300,
            'height' => 300,
        ],
        'medium' => [
            'width' => 600,
            'height' => 600,
        ],
        'large' => [
            'width' => 1200,
            'height' => 1200,
        ],
    ],

    'formats' => [
        'primary' => 'webp',
        'fallback' => 'jpeg',
        'supported' => ['webp', 'jpeg', 'png'],
    ],

    'storage' => [
        'disk' => 'public',
        'directory' => 'products',
    ],

    'lazy_loading' => [
        'enabled' => true,
        'placeholder' => '/images/no-product-image.svg',
    ],

    'responsive' => [
        'enabled' => true,
        'sizes' => '(max-width: 600px) 300px, (max-width: 1200px) 600px, 1200px',
    ],
];
