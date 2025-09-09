<?php

namespace App\Services;

use App\Mail\PriceDropNotificationMail;
use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PriceDropNotificationService
{
    /**
     * Ürün fiyat değişikliğini kontrol et ve gerekirse mail gönder
     */
    public function checkAndNotifyPriceDrop(Product $product, array $oldPrices, array $newPrices)
    {
        try {
            // Fiyat değişikliğini kaydet
            $priceHistory = ProductPriceHistory::recordPriceChange(
                $product->kod,
                $oldPrices,
                $newPrices,
                $product->doviz
            );

            // Eğer indirim varsa, favori kullanıcılara mail gönder
            if ($priceHistory->is_discount) {
                $this->sendPriceDropNotifications($product, $priceHistory);
            }

            return $priceHistory;
        } catch (\Exception $e) {
            Log::error('Fiyat düşüşü bildirimi hatası: ' . $e->getMessage(), [
                'product_kod' => $product->kod,
                'old_prices' => $oldPrices,
                'new_prices' => $newPrices,
            ]);
            throw $e;
        }
    }

    /**
     * Fiyat düşüşü bildirimlerini gönder
     */
    protected function sendPriceDropNotifications(Product $product, ProductPriceHistory $priceHistory)
    {
        // Bu ürünü favorilere ekleyen kullanıcıları bul
        $favoriteUsers = Favorite::where('product_kod', $product->kod)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter(); // null değerleri filtrele

        if ($favoriteUsers->isEmpty()) {
            Log::info('Fiyat düşüşü için favori kullanıcı bulunamadı', [
                'product_kod' => $product->kod,
            ]);
            return;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($favoriteUsers as $user) {
            try {
                // Mail gönder
                Mail::to($user->email)->send(
                    new PriceDropNotificationMail($user, $product, $priceHistory)
                );

                $sentCount++;
                
                Log::info('Fiyat düşüşü bildirimi gönderildi', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'product_kod' => $product->kod,
                    'discount_percentage' => $priceHistory->discount_percentage,
                ]);

            } catch (\Exception $e) {
                $failedCount++;
                
                Log::error('Fiyat düşüşü bildirimi gönderilemedi', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'product_kod' => $product->kod,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Fiyat düşüşü bildirimi tamamlandı', [
            'product_kod' => $product->kod,
            'total_users' => $favoriteUsers->count(),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
            'discount_percentage' => $priceHistory->discount_percentage,
        ]);
    }

    /**
     * Belirli bir ürün için manuel fiyat düşüşü bildirimi gönder
     */
    public function sendManualPriceDropNotification(Product $product, User $user)
    {
        try {
            // Son fiyat değişikliğini bul
            $priceHistory = ProductPriceHistory::where('product_kod', $product->kod)
                ->where('is_discount', true)
                ->orderBy('changed_at', 'desc')
                ->first();

            if (!$priceHistory) {
                throw new \Exception('Bu ürün için fiyat düşüşü geçmişi bulunamadı');
            }

            // Mail gönder
            Mail::to($user->email)->send(
                new PriceDropNotificationMail($user, $product, $priceHistory)
            );

            Log::info('Manuel fiyat düşüşü bildirimi gönderildi', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'product_kod' => $product->kod,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Manuel fiyat düşüşü bildirimi hatası: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'product_kod' => $product->kod,
            ]);
            throw $e;
        }
    }

    /**
     * Toplu fiyat düşüşü bildirimi gönder (cron job için)
     */
    public function sendBulkPriceDropNotifications()
    {
        try {
            // Son 24 saat içindeki indirimleri bul
            $recentDiscounts = ProductPriceHistory::where('is_discount', true)
                ->where('changed_at', '>=', now()->subDay())
                ->with('product')
                ->get();

            Log::info('Toplu fiyat düşüşü bildirimi başladı', [
                'total_discounts' => $recentDiscounts->count(),
                'discounts' => $recentDiscounts->pluck('product_kod')->toArray()
            ]);

            if ($recentDiscounts->isEmpty()) {
                Log::info('Toplu fiyat düşüşü bildirimi: Son 24 saatte indirim bulunamadı');
                return;
            }

            $totalNotifications = 0;
            $totalUsers = 0;

            foreach ($recentDiscounts as $priceHistory) {
                $product = $priceHistory->product;
                if (!$product) continue;

                // Bu ürünü favorilere ekleyen kullanıcıları bul
                $favoriteUsers = Favorite::where('product_kod', $product->kod)
                    ->with('user')
                    ->get()
                    ->pluck('user')
                    ->filter();

                $totalUsers += $favoriteUsers->count();

                foreach ($favoriteUsers as $user) {
                    try {
                        Mail::to($user->email)->send(
                            new PriceDropNotificationMail($user, $product, $priceHistory)
                        );
                        $totalNotifications++;
                    } catch (\Exception $e) {
                        Log::error('Toplu fiyat düşüşü bildirimi hatası', [
                            'user_id' => $user->id,
                            'product_kod' => $product->kod,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            Log::info('Toplu fiyat düşüşü bildirimi tamamlandı', [
                'total_discounts' => $recentDiscounts->count(),
                'total_users' => $totalUsers,
                'total_notifications' => $totalNotifications,
            ]);

        } catch (\Exception $e) {
            Log::error('Toplu fiyat düşüşü bildirimi genel hatası: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Kullanıcının favori ürünlerindeki son indirimleri getir
     */
    public function getUserFavoriteDiscounts(User $user, $limit = 10)
    {
        $favoriteProductKods = Favorite::where('user_id', $user->id)
            ->pluck('product_kod');

        return ProductPriceHistory::whereIn('product_kod', $favoriteProductKods)
            ->where('is_discount', true)
            ->with('product')
            ->orderBy('changed_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
