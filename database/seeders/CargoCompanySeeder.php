<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CargoCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cargoCompanies = [
            [
                'name' => 'Aras Kargo',
                'code' => 'aras',
                'tracking_url' => 'https://www.araskargo.com.tr/takip?code={tracking_number}',
                'is_active' => true,
                'sort_order' => 1,
                'description' => 'Aras Kargo ile güvenli teslimat'
            ],
            [
                'name' => 'Yurtiçi Kargo',
                'code' => 'yurtici',
                'tracking_url' => 'https://www.yurticikargo.com/tr/takip?code={tracking_number}',
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'Yurtiçi Kargo ile hızlı teslimat'
            ],
            [
                'name' => 'MNG Kargo',
                'code' => 'mng',
                'tracking_url' => 'https://www.mngkargo.com.tr/takip?code={tracking_number}',
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'MNG Kargo ile güvenilir teslimat'
            ],
            [
                'name' => 'PTT Kargo',
                'code' => 'ptt',
                'tracking_url' => 'https://www.ptt.gov.tr/takip?code={tracking_number}',
                'is_active' => true,
                'sort_order' => 4,
                'description' => 'PTT Kargo ile geniş ağ'
            ],
            [
                'name' => 'Sürat Kargo',
                'code' => 'surat',
                'tracking_url' => 'https://www.suratkargo.com.tr/takip?code={tracking_number}',
                'is_active' => true,
                'sort_order' => 5,
                'description' => 'Sürat Kargo ile hızlı teslimat'
            ],
            [
                'name' => 'Trendyol Express',
                'code' => 'trendyol',
                'tracking_url' => 'https://www.trendyol.com/takip?code={tracking_number}',
                'is_active' => true,
                'sort_order' => 6,
                'description' => 'Trendyol Express ile hızlı teslimat'
            ]
        ];

        foreach ($cargoCompanies as $company) {
            \App\Models\CargoCompany::create($company);
        }
    }
}
