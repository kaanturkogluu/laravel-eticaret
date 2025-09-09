<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya rate limiting kurallarını içerir.
    |
    */

    'rules' => [
        'login' => [
            'max_attempts' => 5,
            'decay_minutes' => 15,
            'description' => 'Login denemeleri - 5 deneme, 15 dakika'
        ],
        
        'register' => [
            'max_attempts' => 3,
            'decay_minutes' => 60,
            'description' => 'Kayıt denemeleri - 3 deneme, 60 dakika'
        ],
        
        'email-verification' => [
            'max_attempts' => 3,
            'decay_minutes' => 5,
            'description' => 'E-posta doğrulama - 3 deneme, 5 dakika'
        ],
        
        'api' => [
            'max_attempts' => 100,
            'decay_minutes' => 1,
            'description' => 'API istekleri - 100 istek, 1 dakika'
        ],
        
        'cart-add' => [
            'max_attempts' => 20,
            'decay_minutes' => 1,
            'description' => 'Sepete ekleme - 20 istek, 1 dakika'
        ],
        
        'cart-remove' => [
            'max_attempts' => 20,
            'decay_minutes' => 1,
            'description' => 'Sepetten çıkarma - 20 istek, 1 dakika'
        ],
        
        'cart-update' => [
            'max_attempts' => 20,
            'decay_minutes' => 1,
            'description' => 'Sepet güncelleme - 20 istek, 1 dakika'
        ],
        
        'cart-clear' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
            'description' => 'Sepet temizleme - 5 istek, 1 dakika'
        ],
        
        'coupon-apply' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
            'description' => 'Kupon uygulama - 10 istek, 1 dakika'
        ],
        
        'coupon-remove' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
            'description' => 'Kupon kaldırma - 10 istek, 1 dakika'
        ],
        
        'coupon-validate' => [
            'max_attempts' => 20,
            'decay_minutes' => 1,
            'description' => 'Kupon doğrulama - 20 istek, 1 dakika'
        ],
        
        'payment-initiate' => [
            'max_attempts' => 5,
            'decay_minutes' => 1,
            'description' => 'Ödeme başlatma - 5 istek, 1 dakika'
        ],
        
        'payment-status' => [
            'max_attempts' => 10,
            'decay_minutes' => 1,
            'description' => 'Ödeme durumu sorgulama - 10 istek, 1 dakika'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Tüm istekler için genel rate limiting kuralları
    |
    */
    'global' => [
        'max_attempts' => 1000,
        'decay_minutes' => 1,
        'description' => 'Genel rate limiting - 1000 istek, 1 dakika'
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Rate limiting'den muaf tutulacak IP adresleri
    |
    */
    'whitelist' => [
        '127.0.0.1',
        '::1',
        // Admin IP'leri buraya eklenebilir
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Storage
    |--------------------------------------------------------------------------
    |
    | Rate limiting verilerinin saklanacağı cache driver
    |
    */
    'cache_driver' => env('RATE_LIMITING_CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting Messages
    |--------------------------------------------------------------------------
    |
    | Rate limiting mesajları
    |
    */
    'messages' => [
        'too_many_attempts' => 'Çok fazla istek gönderildi. Lütfen :seconds saniye sonra tekrar deneyin.',
        'retry_after' => 'Tekrar deneme süresi: :seconds saniye',
        'rate_limited' => 'Rate limit aşıldı. Lütfen bekleyin.',
    ],
];
