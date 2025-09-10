<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GlobalProfitSetting;
use App\Models\CategoryProfit;
use App\Models\Product;
use Illuminate\Http\Request;

class GlobalProfitController extends Controller
{
    /**
     * Genel kar ayarları sayfası
     */
    public function index()
    {
        $settings = GlobalProfitSetting::getSettings();
        $categoryProfits = CategoryProfit::orderBy('category_name')->get();
        $categories = Product::distinct()->pluck('kategori')->filter()->sort()->values();
        
        return view('admin.global-profit.index', compact('settings', 'categoryProfits', 'categories'));
    }

    /**
     * Genel kar ayarlarını güncelle
     */
    public function update(Request $request)
    {
        $request->validate([
            'is_enabled' => 'boolean',
            'profit_type' => 'required|integer|in:0,1,2',
            'profit_value' => 'required|numeric|min:0'
        ]);

        $data = [
            'is_enabled' => $request->has('is_enabled'),
            'profit_type' => $request->profit_type,
            'profit_value' => $request->profit_value
        ];

        GlobalProfitSetting::updateSettings($data);

        return redirect()->back()->with('success', 'Genel kar ayarları başarıyla güncellendi!');
    }

    /**
     * Kategori kar ayarı ekle/güncelle
     */
    public function storeCategoryProfit(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'profit_type' => 'required|integer|in:0,1,2',
            'profit_value' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $data = $request->only(['category_name', 'profit_type', 'profit_value', 'description']);
        $data['is_active'] = $request->has('is_active');

        CategoryProfit::updateOrCreate(
            ['category_name' => $request->category_name],
            $data
        );

        return redirect()->back()->with('success', 'Kategori kar ayarı başarıyla kaydedildi!');
    }

    /**
     * Kategori kar ayarını güncelle
     */
    public function updateCategoryProfit(Request $request, CategoryProfit $categoryProfit)
    {
        $request->validate([
            'is_active' => 'boolean',
            'profit_type' => 'required|integer|in:0,1,2',
            'profit_value' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);

        $data = $request->only(['profit_type', 'profit_value', 'description']);
        $data['is_active'] = $request->has('is_active');

        $categoryProfit->update($data);

        return redirect()->back()->with('success', 'Kategori kar ayarı başarıyla güncellendi!');
    }

    /**
     * Kategori kar ayarını sil
     */
    public function destroyCategoryProfit(CategoryProfit $categoryProfit)
    {
        $categoryProfit->delete();
        return redirect()->back()->with('success', 'Kategori kar ayarı başarıyla silindi!');
    }

    /**
     * Kategori kar ayarını aktif/pasif yap
     */
    public function toggleCategoryProfit(CategoryProfit $categoryProfit)
    {
        $categoryProfit->update(['is_active' => !$categoryProfit->is_active]);
        
        $status = $categoryProfit->is_active ? 'aktif' : 'pasif';
        return redirect()->back()->with('success', "Kategori kar ayarı {$status} yapıldı!");
    }

    /**
     * Kategori kar ayarı detayını getir (AJAX)
     */
    public function showCategoryProfit(CategoryProfit $categoryProfit)
    {
        return response()->json([
            'id' => $categoryProfit->id,
            'category_name' => $categoryProfit->category_name,
            'is_active' => $categoryProfit->is_active,
            'profit_type' => $categoryProfit->profit_type,
            'profit_value' => $categoryProfit->profit_value,
            'description' => $categoryProfit->description
        ]);
    }
}
