<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Campaign;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Ana sayfa
     */
    public function index()
    {
        // Banner'ları getir
        $banners = Campaign::active()->banners()->ordered()->limit(5)->get();
        
        // Kampanyaları getir
        $campaigns = Campaign::active()->campaigns()->ordered()->limit(3)->get();
        
        // Öne çıkan ürünler (admin tarafından seçilenler)
        $featuredProducts = Product::active()
            ->inStock()
            ->featured()
            ->with(['images'])
            ->limit(12)
            ->get();
            
        // Eğer öne çıkan ürün yoksa, fiyatı düşük olanları göster
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::active()
                ->inStock()
                ->with(['images'])
                ->orderByRaw('LEAST(COALESCE(fiyat_ozel, 999999), COALESCE(fiyat_bayi, 999999), COALESCE(fiyat_sk, 999999)) ASC')
                ->limit(12)
                ->get();
        }

        // Yeni ürünler (son eklenenler)
        $newProducts = Product::active()
            ->inStock()
            ->with(['images'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // En çok stokta olan ürünler
        $popularProducts = Product::active()
            ->inStock()
            ->with(['images'])
            ->orderBy('miktar', 'desc')
            ->limit(8)
            ->get();

        // Kategoriler ve ürün sayıları
        $categories = Product::active()
            ->inStock()
            ->selectRaw('kategori, COUNT(*) as product_count')
            ->whereNotNull('kategori')
            ->groupBy('kategori')
            ->orderBy('product_count', 'desc')
            ->take(8)
            ->get();

        // Markalar ve ürün sayıları
        $brands = Product::active()
            ->inStock()
            ->selectRaw('marka, COUNT(*) as product_count')
            ->whereNotNull('marka')
            ->groupBy('marka')
            ->orderBy('product_count', 'desc')
            ->take(8)
            ->get();

        // İstatistikler
        $stats = [
            'activeProducts' => Product::active()->inStock()->count(),
            'totalBrands' => Product::active()->inStock()->distinct()->count('marka'),
            'totalCategories' => Product::active()->inStock()->distinct()->count('kategori'),
            'totalProducts' => Product::count(),
        ];

        return view('home', compact(
            'banners', 
            'campaigns', 
            'featuredProducts', 
            'newProducts', 
            'popularProducts',
            'categories',
            'brands',
            'stats'
        ));
    }
}