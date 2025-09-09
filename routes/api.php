<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (authentication gerekmez)
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    
    // Protected routes (token gerekli)
    Route::middleware('api.auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::get('/tokens', [AuthController::class, 'tokens']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::post('/revoke-token', [AuthController::class, 'revokeToken']);
        Route::post('/revoke-all-tokens', [AuthController::class, 'revokeAllTokens']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

// Protected API routes (token gerekli)
Route::middleware('api.auth')->group(function () {
    
    // Products API
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'api'])->middleware('api.auth:read');
        Route::get('/{kod}', [ProductController::class, 'apiShow'])->middleware('api.auth:read');
    });
    
    // User profile
    Route::prefix('user')->group(function () {
        Route::get('/profile', function (Request $request) {
            return response()->json([
                'success' => true,
                'data' => $request->user()
            ]);
        });
        
        Route::put('/profile', function (Request $request) {
            $request->validate([
                'name' => 'sometimes|string|max:255',
                'phone' => 'sometimes|nullable|string|max:20',
                'address' => 'sometimes|nullable|string',
            ]);
            
            $user = $request->user();
            $user->update($request->only(['name', 'phone', 'address']));
            
            return response()->json([
                'success' => true,
                'message' => 'Profil güncellendi',
                'data' => $user
            ]);
        });
    });
    
    // Cart API (read permission)
    Route::prefix('cart')->middleware('api.auth:read')->group(function () {
        Route::get('/', function (Request $request) {
            $cartItems = $request->user()->cartItems()->with('product')->get();
            
            return response()->json([
                'success' => true,
                'data' => $cartItems
            ]);
        });
        
        Route::get('/count', function (Request $request) {
            $count = $request->user()->cartItems()->sum('quantity');
            
            return response()->json([
                'success' => true,
                'data' => ['count' => $count]
            ]);
        });
    });
    
    // Cart API (write permission)
    Route::prefix('cart')->middleware('api.auth:write')->group(function () {
        Route::post('/add', function (Request $request) {
            $request->validate([
                'product_kod' => 'required|string|exists:products,kod',
                'quantity' => 'required|integer|min:1|max:10'
            ]);
            
            $user = $request->user();
            $cartItem = $user->cartItems()->where('product_kod', $request->product_kod)->first();
            
            if ($cartItem) {
                $cartItem->update(['quantity' => $cartItem->quantity + $request->quantity]);
            } else {
                $user->cartItems()->create([
                    'product_kod' => $request->product_kod,
                    'quantity' => $request->quantity
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün sepete eklendi'
            ]);
        });
        
        Route::post('/remove', function (Request $request) {
            $request->validate([
                'product_kod' => 'required|string|exists:products,kod'
            ]);
            
            $user = $request->user();
            $user->cartItems()->where('product_kod', $request->product_kod)->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün sepetten çıkarıldı'
            ]);
        });
        
        Route::post('/clear', function (Request $request) {
            $request->user()->cartItems()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Sepet temizlendi'
            ]);
        });
    });
    
    // Favorites API
    Route::prefix('favorites')->middleware('api.auth:read')->group(function () {
        Route::get('/', function (Request $request) {
            $favorites = $request->user()->favoriteProducts()->get();
            
            return response()->json([
                'success' => true,
                'data' => $favorites
            ]);
        });
        
        Route::post('/add', function (Request $request) {
            $request->validate([
                'product_kod' => 'required|string|exists:products,kod'
            ]);
            
            $user = $request->user();
            $user->favoriteProducts()->syncWithoutDetaching([$request->product_kod]);
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün favorilere eklendi'
            ]);
        });
        
        Route::post('/remove', function (Request $request) {
            $request->validate([
                'product_kod' => 'required|string|exists:products,kod'
            ]);
            
            $user = $request->user();
            $user->favoriteProducts()->detach($request->product_kod);
            
            return response()->json([
                'success' => true,
                'message' => 'Ürün favorilerden çıkarıldı'
            ]);
        });
    });
    
    // Orders API (admin permission)
    Route::prefix('orders')->middleware('api.auth:admin')->group(function () {
        Route::get('/', function (Request $request) {
            $orders = $request->user()->orders()->with('items.product')->get();
            
            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        });
        
        Route::get('/{id}', function (Request $request, $id) {
            $order = $request->user()->orders()->with('items.product')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $order
            ]);
        });
    });
});
