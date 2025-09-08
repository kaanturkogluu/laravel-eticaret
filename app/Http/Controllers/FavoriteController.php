<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FavoriteController extends Controller
{
    /**
     * Kullanıcının favori ürünlerini listele
     */
    public function index()
    {
        $user = auth()->user();
        $favorites = $user->favoriteProducts()
            ->with(['images', 'specifications'])
            ->paginate(12);

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Ürünü favorilere ekle
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_kod' => 'required|string|exists:products,kod'
        ]);

        $user = auth()->user();
        $productKod = $request->product_kod;

        // Ürünün var olup olmadığını kontrol et
        $product = Product::where('kod', $productKod)->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı.'
            ], 404);
        }

        // Zaten favorilerde mi kontrol et
        if (Favorite::isFavorite($user->id, $productKod)) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürün zaten favorilerinizde.'
            ], 400);
        }

        // Favorilere ekle
        Favorite::addToFavorites($user->id, $productKod);

        return response()->json([
            'success' => true,
            'message' => 'Ürün favorilere eklendi.',
            'is_favorite' => true
        ]);
    }

    /**
     * Ürünü favorilerden çıkar
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_kod' => 'required|string|exists:products,kod'
        ]);

        $user = auth()->user();
        $productKod = $request->product_kod;

        // Favorilerden çıkar
        $removed = Favorite::removeFromFavorites($user->id, $productKod);

        if (!$removed) {
            return response()->json([
                'success' => false,
                'message' => 'Bu ürün favorilerinizde bulunamadı.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ürün favorilerden çıkarıldı.',
            'is_favorite' => false
        ]);
    }

    /**
     * Favori durumunu toggle et (ekle/çıkar)
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_kod' => 'required|string|exists:products,kod'
        ]);

        $user = auth()->user();
        $productKod = $request->product_kod;

        // Ürünün var olup olmadığını kontrol et
        $product = Product::where('kod', $productKod)->first();
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı.'
            ], 404);
        }

        // Favori durumunu kontrol et
        $isFavorite = Favorite::isFavorite($user->id, $productKod);

        if ($isFavorite) {
            // Favorilerden çıkar
            Favorite::removeFromFavorites($user->id, $productKod);
            $message = 'Ürün favorilerden çıkarıldı.';
            $isFavorite = false;
        } else {
            // Favorilere ekle
            Favorite::addToFavorites($user->id, $productKod);
            $message = 'Ürün favorilere eklendi.';
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Belirli bir ürünün favori durumunu kontrol et
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'product_kod' => 'required|string|exists:products,kod'
        ]);

        $user = auth()->user();
        $productKod = $request->product_kod;

        $isFavorite = Favorite::isFavorite($user->id, $productKod);

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Kullanıcının favori sayısını getir
     */
    public function count(): JsonResponse
    {
        $user = auth()->user();
        $count = $user->favorites()->count();

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }
}
