<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Configuration
    |--------------------------------------------------------------------------
    |
    | Bu dosya ödeme sistemi için genel konfigürasyonları içerir.
    |
    */

    'default_currency' => env('PAYMENT_DEFAULT_CURRENCY', 'TRY'),

    'supported_currencies' => [
        'TRY' => 'Türk Lirası',
        'USD' => 'Amerikan Doları',
        'EUR' => 'Euro',
        'GBP' => 'İngiliz Sterlini',
    ],

    'supported_payment_methods' => [
        'credit_card' => 'Kredi Kartı',
        'bank_transfer' => 'Banka Havalesi',
        'wallet' => 'Cüzdan',
        'cash_on_delivery' => 'Kapıda Ödeme',
    ],

    'default_urls' => [
        'success' => env('PAYMENT_SUCCESS_URL', '/payment/success'),
        'failure' => env('PAYMENT_FAILURE_URL', '/payment/failure'),
        'cancel' => env('PAYMENT_CANCEL_URL', '/payment/cancel'),
        'callback' => env('PAYMENT_CALLBACK_URL', '/payment/callback'),
        'webhook' => env('PAYMENT_WEBHOOK_URL', '/payment/webhook'),
    ],

    'timeout' => [
        'http_request' => env('PAYMENT_HTTP_TIMEOUT', 30),
        'payment_process' => env('PAYMENT_PROCESS_TIMEOUT', 300), // 5 dakika
    ],

    'retry' => [
        'max_attempts' => env('PAYMENT_RETRY_MAX_ATTEMPTS', 3),
        'delay' => env('PAYMENT_RETRY_DELAY', 5), // saniye
    ],

    'security' => [
        'hash_algorithm' => env('PAYMENT_HASH_ALGORITHM', 'sha256'),
        'verify_ip' => env('PAYMENT_VERIFY_IP', false),
        'log_sensitive_data' => env('PAYMENT_LOG_SENSITIVE_DATA', false),
    ],

    'commission' => [
        'default_rate' => env('PAYMENT_DEFAULT_COMMISSION_RATE', 0),
        'default_fixed' => env('PAYMENT_DEFAULT_COMMISSION_FIXED', 0),
    ],

    'limits' => [
        'min_amount' => env('PAYMENT_MIN_AMOUNT', 0.01),
        'max_amount' => env('PAYMENT_MAX_AMOUNT', 100000),
    ],

    'providers' => [
        'payu' => [
            'name' => 'PayU',
            'description' => 'PayU ödeme sistemi',
            'logo' => '/images/payment-logos/payu.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'notify_url' => ['label' => 'Notify URL', 'type' => 'url', 'required' => true],
                'continue_url' => ['label' => 'Continue URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY', 'USD', 'EUR'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.5,
            'commission_fixed' => 0.50,
        ],

        'sipay' => [
            'name' => 'Sipay',
            'description' => 'Sipay ödeme sistemi',
            'logo' => '/images/payment-logos/sipay.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_key' => ['label' => 'Merchant Key', 'type' => 'text', 'required' => true],
                'merchant_secret' => ['label' => 'Merchant Secret', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.0,
            'commission_fixed' => 0.30,
        ],

        'mokapos' => [
            'name' => 'Mokapos',
            'description' => 'Mokapos ödeme sistemi',
            'logo' => '/images/payment-logos/mokapos.png',
            'test_mode' => true,
            'config_fields' => [
                'api_key' => ['label' => 'API Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'cancel_url' => ['label' => 'Cancel URL', 'type' => 'url', 'required' => true],
                'webhook_url' => ['label' => 'Webhook URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'wallet'],
            'commission_rate' => 1.8,
            'commission_fixed' => 0.25,
        ],

        'paynet' => [
            'name' => 'Paynet',
            'description' => 'Paynet ödeme sistemi',
            'logo' => '/images/payment-logos/paynet.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.2,
            'commission_fixed' => 0.40,
        ],

        'odeal' => [
            'name' => 'Ödeal',
            'description' => 'Ödeal ödeme sistemi',
            'logo' => '/images/payment-logos/odeal.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.0,
            'commission_fixed' => 0.35,
        ],

        'papara' => [
            'name' => 'Papara',
            'description' => 'Papara ödeme sistemi',
            'logo' => '/images/payment-logos/papara.png',
            'test_mode' => true,
            'config_fields' => [
                'api_key' => ['label' => 'API Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['wallet'],
            'commission_rate' => 1.5,
            'commission_fixed' => 0.20,
        ],

        'paycell' => [
            'name' => 'Paycell',
            'description' => 'Paycell ödeme sistemi',
            'logo' => '/images/payment-logos/paycell.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'wallet'],
            'commission_rate' => 2.3,
            'commission_fixed' => 0.45,
        ],

        'hepsipay' => [
            'name' => 'Hepsipay',
            'description' => 'Hepsipay ödeme sistemi',
            'logo' => '/images/payment-logos/hepsipay.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.1,
            'commission_fixed' => 0.38,
        ],

        'esnekpos' => [
            'name' => 'Esnekpos',
            'description' => 'Esnekpos ödeme sistemi',
            'logo' => '/images/payment-logos/esnekpos.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.0,
            'commission_fixed' => 0.30,
        ],

        'ininal' => [
            'name' => 'Ininal',
            'description' => 'Ininal ödeme sistemi',
            'logo' => '/images/payment-logos/ininal.png',
            'test_mode' => true,
            'config_fields' => [
                'api_key' => ['label' => 'API Key', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['wallet'],
            'commission_rate' => 1.8,
            'commission_fixed' => 0.25,
        ],

        'paytrek' => [
            'name' => 'Paytrek',
            'description' => 'Paytrek ödeme sistemi',
            'logo' => '/images/payment-logos/paytrek.png',
            'test_mode' => true,
            'config_fields' => [
                'merchant_id' => ['label' => 'Merchant ID', 'type' => 'text', 'required' => true],
                'secret_key' => ['label' => 'Secret Key', 'type' => 'password', 'required' => true],
                'test_api_url' => ['label' => 'Test API URL', 'type' => 'url', 'required' => true],
                'live_api_url' => ['label' => 'Live API URL', 'type' => 'url', 'required' => true],
                'success_url' => ['label' => 'Success URL', 'type' => 'url', 'required' => true],
                'fail_url' => ['label' => 'Fail URL', 'type' => 'url', 'required' => true],
                'callback_url' => ['label' => 'Callback URL', 'type' => 'url', 'required' => true],
            ],
            'supported_currencies' => ['TRY'],
            'supported_payment_methods' => ['credit_card', 'bank_transfer'],
            'commission_rate' => 2.4,
            'commission_fixed' => 0.50,
        ],
    ],

    'logging' => [
        'channel' => env('PAYMENT_LOG_CHANNEL', 'payment'),
        'level' => env('PAYMENT_LOG_LEVEL', 'info'),
        'log_requests' => env('PAYMENT_LOG_REQUESTS', true),
        'log_responses' => env('PAYMENT_LOG_RESPONSES', true),
        'log_errors' => env('PAYMENT_LOG_ERRORS', true),
    ],

    'notifications' => [
        'enabled' => env('PAYMENT_NOTIFICATIONS_ENABLED', true),
        'channels' => [
            'mail' => env('PAYMENT_MAIL_NOTIFICATIONS', true),
            'slack' => env('PAYMENT_SLACK_NOTIFICATIONS', false),
            'webhook' => env('PAYMENT_WEBHOOK_NOTIFICATIONS', false),
        ],
        'events' => [
            'payment_success' => env('PAYMENT_NOTIFY_SUCCESS', true),
            'payment_failure' => env('PAYMENT_NOTIFY_FAILURE', true),
            'payment_refund' => env('PAYMENT_NOTIFY_REFUND', true),
        ],
    ],
];
