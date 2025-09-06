<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Ürün listesi sayfası
     */
    public function index(Request $request)
    {
        $query = Product::active()->inStock()->with(['images', 'specifications']);

        // Arama
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Marka filtresi
        if ($request->filled('brand')) {
            $query->byBrand($request->brand);
        }

        // Kategori filtresi
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Sıralama
        $sortBy = $request->get('sort', 'ad');
        $sortDirection = $request->get('direction', 'asc');
        
        switch ($sortBy) {
            case 'price':
                $query->orderByRaw('LEAST(COALESCE(fiyat_ozel, 999999), COALESCE(fiyat_bayi, 999999), COALESCE(fiyat_sk, 999999)) ' . $sortDirection);
                break;
            case 'stock':
                $query->orderBy('miktar', $sortDirection);
                break;
            default:
                $query->orderBy($sortBy, $sortDirection);
        }

        $products = $query->paginate(20);

        // Filtreler için veriler
        $brands = Product::active()->inStock()->distinct()->pluck('marka')->filter()->sort()->values();
        $categories = Product::active()->inStock()->distinct()->pluck('kategori')->filter()->sort()->values();

        return view('products.index', compact('products', 'brands', 'categories'));
    }

    /**
     * Ürün detay sayfası
     */
    public function show($kod)
    {
        $product = Product::active()
            ->where('kod', $kod)
            ->with(['images', 'specifications'])
            ->firstOrFail();

        return view('products.show', compact('product'));
    }

    /**
     * API - Ürün listesi (AJAX için)
     */
    public function api(Request $request)
    {
        $query = Product::active()->inStock()->with(['images']);

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('brand')) {
            $query->byBrand($request->brand);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $products = $query->paginate(20);

        return response()->json($products);
    }
}
