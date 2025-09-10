<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slider;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Teknoloji Dünyasına Hoş Geldiniz',
                'description' => 'En yeni teknoloji ürünleri ve elektronik eşyalar için doğru adres. Kaliteli ürünler, uygun fiyatlar.',
                'image_url' => 'sliders/tech-slider-1.jpg',
                'link_url' => '/products',
                'link_text' => 'Ürünleri İncele',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Kampanyalı Ürünler',
                'description' => 'Sınırlı süre için özel indirimler! Kaçırmayın, hemen alışverişe başlayın.',
                'image_url' => 'sliders/sale-slider-2.jpg',
                'link_url' => '/products?campaign=true',
                'link_text' => 'Kampanyaları Gör',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Yeni Gelen Ürünler',
                'description' => 'En son teknoloji ürünleri artık sitemizde! İlk siz keşfedin.',
                'image_url' => 'sliders/new-products-slider-3.jpg',
                'link_url' => '/products?sort=newest',
                'link_text' => 'Yeni Ürünler',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}