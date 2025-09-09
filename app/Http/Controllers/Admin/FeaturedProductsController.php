<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeaturedProductsController extends Controller
{
    /**
     * Öne çıkan ürünler listesi
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 12);

        // Öne çıkan ürünler
        $featuredQuery = Product::active()
            ->inStock()
            ->featured()
            ->with('images');

        if ($search) {
            $featuredQuery->search($search);
        }

        $featuredProducts = $featuredQuery->get();

        // Tüm ürünler (sayfalama ile)
        $allProductsQuery = Product::active()
            ->inStock()
            ->where('featured', false)
            ->with('images');

        if ($search) {
            $allProductsQuery->search($search);
        }

        $allProducts = $allProductsQuery->orderBy('ad')
            ->paginate($perPage)
            ->withQueryString();

        return view('admin.featured-products.index', compact('featuredProducts', 'allProducts', 'search', 'perPage'));
    }

    /**
     * Ürünü öne çıkan olarak ekle
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'featured_order' => 'nullable|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);
        
        // Eğer sıra belirtilmemişse, en son sırayı al
        if (!$request->featured_order) {
            $maxOrder = Product::where('featured', true)->max('featured_order') ?? 0;
            $request->featured_order = $maxOrder + 1;
        }

        $product->update([
            'featured' => true,
            'featured_order' => $request->featured_order
        ]);

        // Arama parametrelerini koru
        $redirectParams = [];
        if ($request->has('search')) {
            $redirectParams['search'] = $request->search;
        }
        if ($request->has('per_page') && $request->per_page != 12) {
            $redirectParams['per_page'] = $request->per_page;
        }

        return redirect()->route('admin.featured-products.index', $redirectParams)
            ->with('success', 'Ürün öne çıkan ürünler listesine eklendi.');
    }

    /**
     * Ürünü öne çıkan listesinden çıkar
     */
    public function remove(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $product->update([
            'featured' => false,
            'featured_order' => null
        ]);

        // Arama parametrelerini koru
        $redirectParams = [];
        if ($request->has('search')) {
            $redirectParams['search'] = $request->search;
        }
        if ($request->has('per_page') && $request->per_page != 12) {
            $redirectParams['per_page'] = $request->per_page;
        }

        return redirect()->route('admin.featured-products.index', $redirectParams)
            ->with('success', 'Ürün öne çıkan ürünler listesinden çıkarıldı.');
    }

    /**
     * Öne çıkan ürünlerin sırasını güncelle
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.order' => 'required|integer|min:1'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->products as $productData) {
                Product::where('id', $productData['id'])
                    ->update(['featured_order' => $productData['order']]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Sıralama güncellendi.']);
    }

    /**
     * Öne çıkan ürünlerin sırasını sıfırla
     */
    public function resetOrder()
    {
        Product::where('featured', true)
            ->update(['featured_order' => null]);

        return redirect()->route('admin.featured-products.index')
            ->with('success', 'Öne çıkan ürünlerin sırası sıfırlandı.');
    }
}