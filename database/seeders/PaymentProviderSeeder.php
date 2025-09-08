<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = config('payment.providers');

        foreach ($providers as $code => $providerConfig) {
            \App\Models\PaymentProvider::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $providerConfig['name'],
                    'description' => $providerConfig['description'],
                    'logo_url' => $providerConfig['logo'],
                    'is_active' => true,
                    'sort_order' => array_search($code, array_keys($providers)),
                    'supported_currencies' => $providerConfig['supported_currencies'],
                    'supported_payment_methods' => $providerConfig['supported_payment_methods'],
                    'commission_rate' => $providerConfig['commission_rate'],
                    'commission_fixed' => $providerConfig['commission_fixed'],
                    'test_mode' => $providerConfig['test_mode'],
                    'config' => [
                        'test_api_url' => '',
                        'live_api_url' => '',
                        'merchant_id' => '',
                        'api_key' => '',
                        'secret_key' => '',
                        'success_url' => config('payment.default_urls.success'),
                        'fail_url' => config('payment.default_urls.failure'),
                        'cancel_url' => config('payment.default_urls.cancel'),
                        'callback_url' => config('payment.default_urls.callback') . '/' . $code,
                        'webhook_url' => config('payment.default_urls.webhook') . '/' . $code,
                    ]
                ]
            );
        }
    }
}
