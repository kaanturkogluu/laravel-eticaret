<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MarketplaceController extends Controller
{
    public function testHepsiburadaConnection(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'seller_id' => 'required|string'
        ]);

        try {
            // Hepsiburada API test endpoint'i
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->api_key,
                'Content-Type' => 'application/json'
            ])->get('https://mpop.hepsiburada.com/product/api/products', [
                'merchantId' => $request->seller_id,
                'page' => 1,
                'size' => 1
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Hepsiburada bağlantısı başarılı!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Hepsiburada bağlantısı başarısız: ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage()
            ]);
        }
    }

    public function testTrendyolConnection(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'seller_id' => 'required|string'
        ]);

        try {
            // Trendyol API test endpoint'i
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($request->api_key . ':' . $request->seller_id),
                'Content-Type' => 'application/json'
            ])->get('https://api.trendyol.com/sapigw/suppliers/' . $request->seller_id . '/products');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trendyol bağlantısı başarılı!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Trendyol bağlantısı başarısız: ' . $response->body()
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage()
            ]);
        }
    }

    public function syncToHepsiburada(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'seller_id' => 'required|string',
            'product_ids' => 'nullable|array'
        ]);

        try {
            $products = $request->product_ids 
                ? Product::whereIn('id', $request->product_ids)->get()
                : Product::active()->inStock()->get();

            $syncedCount = 0;
            $failedCount = 0;

            foreach ($products as $product) {
                $productData = [
                    'merchantId' => $request->seller_id,
                    'productName' => $product->ad,
                    'categoryName' => $product->kategori,
                    'brand' => $product->marka,
                    'price' => $product->fiyat_ozel,
                    'stock' => $product->miktar,
                    'description' => $product->aciklama,
                    'images' => $product->images->pluck('resim_url')->toArray()
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $request->api_key,
                    'Content-Type' => 'application/json'
                ])->post('https://mpop.hepsiburada.com/product/api/products', $productData);

                if ($response->successful()) {
                    $syncedCount++;
                } else {
                    $failedCount++;
                    Log::error('Hepsiburada sync failed for product: ' . $product->id, [
                        'response' => $response->body()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Senkronizasyon tamamlandı! Başarılı: {$syncedCount}, Başarısız: {$failedCount}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage()
            ]);
        }
    }

    public function syncToTrendyol(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'seller_id' => 'required|string',
            'product_ids' => 'nullable|array'
        ]);

        try {
            $products = $request->product_ids 
                ? Product::whereIn('id', $request->product_ids)->get()
                : Product::active()->inStock()->get();

            $syncedCount = 0;
            $failedCount = 0;

            foreach ($products as $product) {
                $productData = [
                    'barcode' => $product->kod,
                    'title' => $product->ad,
                    'categoryName' => $product->kategori,
                    'brand' => $product->marka,
                    'listPrice' => $product->fiyat_ozel,
                    'salePrice' => $product->fiyat_ozel,
                    'quantity' => $product->miktar,
                    'description' => $product->aciklama,
                    'images' => $product->images->pluck('resim_url')->toArray()
                ];

                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($request->api_key . ':' . $request->seller_id),
                    'Content-Type' => 'application/json'
                ])->post('https://api.trendyol.com/sapigw/suppliers/' . $request->seller_id . '/products', $productData);

                if ($response->successful()) {
                    $syncedCount++;
                } else {
                    $failedCount++;
                    Log::error('Trendyol sync failed for product: ' . $product->id, [
                        'response' => $response->body()
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Senkronizasyon tamamlandı! Başarılı: {$syncedCount}, Başarısız: {$failedCount}"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Senkronizasyon hatası: ' . $e->getMessage()
            ]);
        }
    }

    public function saveSyncSettings(Request $request)
    {
        $request->validate([
            'sync_frequency' => 'required|integer|min:30',
            'auto_stock_update' => 'required|boolean',
            'auto_price_update' => 'required|boolean'
        ]);

        // Bu ayarları veritabanında saklayabilirsiniz
        // Şimdilik session'da saklıyoruz
        session([
            'sync_frequency' => $request->sync_frequency,
            'auto_stock_update' => $request->auto_stock_update,
            'auto_price_update' => $request->auto_price_update
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senkronizasyon ayarları kaydedildi!'
        ]);
    }
}
