<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Ürün listesi
     */
    public function index(Request $request)
    {
        $query = Product::with(['images']);

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

        // Durum filtresi
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Sıralama
        $sortBy = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $products = $query->paginate(20);

        // Filtreler için veriler
        $brands = Product::distinct()->pluck('marka')->filter()->sort()->values();
        $categories = Product::distinct()->pluck('kategori')->filter()->sort()->values();

        return view('admin.products.index', compact('products', 'brands', 'categories'));
    }

    /**
     * Yeni ürün formu
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Ürün düzenleme formu
     */
    public function edit(Product $product)
    {
        $product->load(['images', 'specifications']);
        return view('admin.products.edit', compact('product'));
    }

    public function store(Request $request)
    {
        \Log::info('Manuel ürün ekleme başladı', [
            'request_data' => $request->except(['images']),
            'has_images' => $request->hasFile('images'),
            'files' => $request->allFiles()
        ]);

        $request->validate([
            'ad' => 'required|string|max:255',
            'kod' => 'required|string|max:255|unique:products,kod',
            'marka' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'fiyat_ozel' => 'required|numeric|min:0',
            'miktar' => 'required|integer|min:0',
            'doviz' => 'required|string|in:TL,USD,EUR',
            'aciklama' => 'nullable|string',
            'is_active' => 'boolean',
            'profit_enabled' => 'boolean',
            'profit_type' => 'integer|in:0,1,2',
            'profit_value' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        $product = Product::create([
            'ad' => $request->ad,
            'kod' => $request->kod,
            'marka' => $request->marka,
            'kategori' => $request->kategori,
            'fiyat_ozel' => $request->fiyat_ozel,
            'miktar' => $request->miktar,
            'doviz' => $request->doviz,
            'aciklama' => $request->aciklama,
            'is_active' => $request->has('is_active'),
            'profit_enabled' => $request->has('profit_enabled'),
            'profit_type' => $request->profit_type ?? 0,
            'profit_value' => $request->profit_value ?? 0
        ]);

        // Çoklu resim upload işlemi
        if ($request->hasFile('images')) {
            \Log::info('Ürün oluşturma - resim upload başladı', [
                'product_id' => $product->id,
                'image_count' => count($request->file('images'))
            ]);

            foreach ($request->file('images') as $index => $imageFile) {
                try {
                    $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
                    $result = $imageOptimizationService->optimizeAndStore($imageFile, 'products');
                    
                    if ($result['success']) {
                        ProductImage::create([
                            'product_id' => $product->id,
                            'urun_kodu' => $product->kod,
                            'resim_url' => $result['original_url'],
                            'sort_order' => $index
                        ]);
                        
                        \Log::info('Ürün oluşturma - resim başarıyla yüklendi', [
                            'product_id' => $product->id,
                            'image_index' => $index,
                            'sort_order' => $index,
                            'original_url' => $result['original_url']
                        ]);
                    } else {
                        \Log::error('Ürün oluşturma - resim optimizasyon hatası', [
                            'error' => $result['error'],
                            'product_id' => $product->id,
                            'image_index' => $index
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Ürün oluşturma - resim yükleme hatası', [
                        'error' => $e->getMessage(),
                        'product_id' => $product->id,
                        'image_index' => $index
                    ]);
                }
            }
        } else {
            \Log::info('Resim dosyası bulunamadı', [
                'files' => $request->allFiles(),
                'has_file' => $request->hasFile('images')
            ]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Ürün başarıyla eklendi!',
                'product' => $product
            ]);
        }
        
        return redirect()->back()->with('success', 'Ürün başarıyla eklendi!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'ad' => 'required|string|max:255',
            'kod' => 'required|string|max:255|unique:products,kod,' . $product->id,
            'marka' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'fiyat_ozel' => 'required|numeric|min:0',
            'fiyat_bayi' => 'nullable|numeric|min:0',
            'fiyat_sk' => 'nullable|numeric|min:0',
            'miktar' => 'required|integer|min:0',
            'doviz' => 'required|string|in:TL,USD,EUR',
            'aciklama' => 'nullable|string',
            'is_active' => 'boolean',
            'profit_enabled' => 'boolean',
            'profit_type' => 'integer|in:0,1,2',
            'profit_value' => 'nullable|numeric|min:0',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
        ]);

        // Checkbox'ları düzgün işle
        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['profit_enabled'] = $request->has('profit_enabled');

        // Fiyat kontrolü ile güncelle
        $product->updateWithPriceCheck($data);

        // Çoklu resim upload işlemi
        if ($request->hasFile('images')) {
            \Log::info('Ürün güncelleme - resim upload başladı', [
                'product_id' => $product->id,
                'image_count' => count($request->file('images'))
            ]);

            foreach ($request->file('images') as $index => $imageFile) {
                try {
                    $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
                    $result = $imageOptimizationService->optimizeAndStore($imageFile, 'products');
                    
                    if ($result['success']) {
                        // Mevcut resimlerin en yüksek sort_order değerini bul
                        $lastSortOrder = $product->images()->max('sort_order') ?? -1;
                        
                        \App\Models\ProductImage::create([
                            'product_id' => $product->id,
                            'urun_kodu' => $product->kod,
                            'resim_url' => $result['original_url'],
                            'sort_order' => $lastSortOrder + 1 + $index
                        ]);
                        
                        \Log::info('Ürün güncelleme - resim başarıyla yüklendi', [
                            'product_id' => $product->id,
                            'image_index' => $index,
                            'sort_order' => $lastSortOrder + 1 + $index,
                            'original_url' => $result['original_url']
                        ]);
                    } else {
                        \Log::error('Ürün güncelleme - resim optimizasyon hatası', [
                            'error' => $result['error'],
                            'product_id' => $product->id,
                            'image_index' => $index
                        ]);
                    }
                } catch (\Exception $e) {
                    \Log::error('Ürün güncelleme - resim yükleme hatası', [
                        'error' => $e->getMessage(),
                        'product_id' => $product->id,
                        'image_index' => $index
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Ürün başarıyla güncellendi!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Ürün başarıyla silindi!');
    }

    /**
     * Ürün resmini sil
     */
    public function deleteImage($imageId)
    {
        try {
            $image = \App\Models\ProductImage::findOrFail($imageId);
            
            // Dosyayı sil
            if (file_exists(public_path($image->resim_url))) {
                unlink(public_path($image->resim_url));
            }
            
            // Veritabanından sil
            $image->delete();
            
            \Log::info('Ürün resmi silindi', [
                'image_id' => $imageId,
                'resim_url' => $image->resim_url
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Resim başarıyla silindi.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Resim silme hatası', [
                'image_id' => $imageId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Resim silinirken bir hata oluştu.'
            ], 500);
        }
    }
}
