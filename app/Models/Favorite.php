<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'product_kod',
    ];

    /**
     * Favori sahibi kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Favori ürün
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_kod', 'kod');
    }

    /**
     * Belirli bir kullanıcının favori ürünlerini getir
     */
    public static function getUserFavorites($userId)
    {
        return self::where('user_id', $userId)
            ->with('product.images')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Kullanıcının belirli bir ürünü favorilere ekleyip eklemediğini kontrol et
     */
    public static function isFavorite($userId, $productKod)
    {
        return self::where('user_id', $userId)
            ->where('product_kod', $productKod)
            ->exists();
    }

    /**
     * Favori ekle
     */
    public static function addToFavorites($userId, $productKod)
    {
        return self::firstOrCreate([
            'user_id' => $userId,
            'product_kod' => $productKod,
        ]);
    }

    /**
     * Favoriden çıkar
     */
    public static function removeFromFavorites($userId, $productKod)
    {
        return self::where('user_id', $userId)
            ->where('product_kod', $productKod)
            ->delete();
    }
}
