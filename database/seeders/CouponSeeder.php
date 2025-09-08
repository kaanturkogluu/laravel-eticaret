<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Coupon;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test kuponları oluştur
        $coupons = [
            [
                'code' => 'WELCOME10',
                'name' => 'Hoş Geldin İndirimi',
                'description' => 'İlk alışverişinizde %10 indirim',
                'type' => 'percentage',
                'value' => 10,
                'minimum_amount' => 100,
                'usage_limit' => 100,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'SAVE50',
                'name' => '50 TL İndirim',
                'description' => '200 TL ve üzeri alışverişlerde 50 TL indirim',
                'type' => 'fixed_amount',
                'value' => 50,
                'minimum_amount' => 200,
                'usage_limit' => 50,
                'usage_limit_per_user' => 2,
                'is_active' => true,
            ],
            [
                'code' => 'FREESHIP',
                'name' => 'Ücretsiz Kargo',
                'description' => 'Ücretsiz kargo kuponu',
                'type' => 'fixed_amount',
                'value' => 0,
                'free_shipping' => true,
                'usage_limit' => 200,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ],
            [
                'code' => 'SUMMER20',
                'name' => 'Yaz İndirimi',
                'description' => 'Yaz sezonu %20 indirim',
                'type' => 'percentage',
                'value' => 20,
                'maximum_discount' => 100,
                'minimum_amount' => 150,
                'usage_limit' => 75,
                'usage_limit_per_user' => 1,
                'is_active' => true,
            ]
        ];

        foreach ($coupons as $couponData) {
            Coupon::create($couponData);
        }
    }
}
