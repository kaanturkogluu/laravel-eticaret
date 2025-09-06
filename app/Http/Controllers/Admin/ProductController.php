<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ad' => 'required|string|max:255',
            'kod' => 'required|string|max:255|unique:products,kod',
            'marka' => 'nullable|string|max:255',
            'kategori' => 'nullable|string|max:255',
            'fiyat_ozel' => 'required|numeric|min:0',
            'miktar' => 'required|integer|min:0',
            'doviz' => 'required|string|in:TL,USD,EUR',
            'aciklama' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
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
            $imagePath = $request->file('image')->store('products', 'public');
            ProductImage::create([
                'product_id' => $product->id,
                'resim_url' => Storage::url($imagePath),
                'is_primary' => true
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
            'miktar' => 'required|integer|min:0',
            'doviz' => 'required|string|in:TL,USD,EUR',
            'aciklama' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $product->update($request->all());

        return redirect()->back()->with('success', 'Ürün başarıyla güncellendi!');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Ürün başarıyla silindi!');
    }
}
