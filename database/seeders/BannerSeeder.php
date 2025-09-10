<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Banner 1
        Campaign::create([
            'title' => 'Yeni Sezon İndirimleri',
            'description' => 'Tüm ürünlerde %30\'a varan indirimler!',
            'image_url' => 'https://via.placeholder.com/1200x500/2563eb/ffffff?text=Yeni+Sezon+İndirimleri',
            'link_url' => '/products',
            'type' => 'banner',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        // Banner 2
        Campaign::create([
            'title' => 'Teknoloji Fırsatları',
            'description' => 'En yeni teknoloji ürünleri burada!',
            'image_url' => 'https://via.placeholder.com/1200x500/1e40af/ffffff?text=Teknoloji+Fırsatları',
            'link_url' => '/products?category=Telefon',
            'type' => 'banner',
            'is_active' => true,
            'sort_order' => 2,
        ]);

        // Banner 3
        Campaign::create([
            'title' => 'Hızlı Teslimat',
            'description' => '24 saat içinde kapınızda!',
            'image_url' => 'https://via.placeholder.com/1200x500/059669/ffffff?text=Hızlı+Teslimat',
            'link_url' => '/products?sort=created_at&direction=desc',
            'type' => 'banner',
            'is_active' => true,
            'sort_order' => 3,
        ]);

        // Banner 4
        Campaign::create([
            'title' => 'Premium Ürünler',
            'description' => 'Kaliteli markaların seçkin ürünleri',
            'image_url' => 'https://via.placeholder.com/1200x500/dc2626/ffffff?text=Premium+Ürünler',
            'link_url' => '/products?featured=1',
            'type' => 'banner',
            'is_active' => true,
            'sort_order' => 4,
        ]);

        // Banner 5
        Campaign::create([
            'title' => 'Kampanya Merkezi',
            'description' => 'Güncel kampanyaları kaçırmayın!',
            'image_url' => 'https://via.placeholder.com/1200x500/7c3aed/ffffff?text=Kampanya+Merkezi',
            'link_url' => '/campaigns',
            'type' => 'banner',
            'is_active' => true,
            'sort_order' => 5,
        ]);
    }
}
