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
            'request_data' => $request->except(['image']),
            'has_image' => $request->hasFile('image'),
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120'
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
            'is_active' => true
        ]);

        if ($request->hasFile('image')) {
            try {
                $imagePath = $request->file('image')->store('products', 'public');
                $imageUrl = Storage::url($imagePath);
                
                ProductImage::create([
                    'product_id' => $product->id,
                    'urun_kodu' => $product->kod,
                    'resim_url' => $imageUrl,
                    'sort_order' => 0
                ]);
                
                \Log::info('Resim başarıyla yüklendi', [
                    'product_id' => $product->id,
                    'image_path' => $imagePath,
                    'image_url' => $imageUrl
                ]);
            } catch (\Exception $e) {
                \Log::error('Resim yükleme hatası', [
                    'error' => $e->getMessage(),
                    'product_id' => $product->id
                ]);
            }
        } else {
            \Log::info('Resim dosyası bulunamadı', [
                'files' => $request->allFiles(),
                'has_file' => $request->hasFile('image')
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
            'is_active' => 'boolean'
        ]);

        // Fiyat kontrolü ile güncelle
        $product->updateWithPriceCheck($request->all());

        return redirect()->back()->with('success', 'Ürün başarıyla güncellendi!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Ürün başarıyla silindi!');
    }
}
