<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Sepet sayfası
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();
        $cartTotalInTry = Cart::getCartTotalInTry(Auth::id(), Session::getId());
        $cartCount = $this->getCartCount();

        return view('cart.index', compact('cartItems', 'cartTotal', 'cartTotalInTry', 'cartCount'));
    }

    /**
     * Sepete ürün ekle
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1|max:10'
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        $userId = Auth::id();
        $sessionId = Session::getId();

        // Ürün stok kontrolü
        $product = Product::findOrFail($productId);
        if ($product->miktar < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Yeterli stok bulunmuyor. Mevcut stok: ' . $product->miktar
            ]);
        }

        // Sepette var mı kontrol et
        $cartItem = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->where('product_id', $productId)->first();

        if ($cartItem) {
            // Mevcut ürünün miktarını artır
            $newQuantity = $cartItem->quantity + $quantity;
            
            if ($product->miktar < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yeterli stok bulunmuyor. Mevcut stok: ' . $product->miktar
                ]);
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Yeni ürün ekle
            Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'session_id' => $sessionId
            ]);
        }

        $cartCount = $this->getCartCount();

        return response()->json([
            'success' => true,
            'message' => 'Ürün sepete eklendi',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Sepetten ürün çıkar
     */
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $productId = $request->product_id;
        $userId = Auth::id();
        $sessionId = Session::getId();

        $cartItem = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->delete();
        }

        $cartCount = $this->getCartCount();
        $cartTotal = $this->getCartTotal();

        return response()->json([
            'success' => true,
            'message' => 'Ürün sepetten çıkarıldı',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    /**
     * Sepet miktarını güncelle
     */
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;
        $userId = Auth::id();
        $sessionId = Session::getId();

        // Ürün stok kontrolü
        $product = Product::findOrFail($productId);
        if ($product->miktar < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Yeterli stok bulunmuyor. Mevcut stok: ' . $product->miktar
            ]);
        }

        $cartItem = Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->where('product_id', $productId)->first();

        if ($cartItem) {
            $cartItem->update(['quantity' => $quantity]);
        }

        $cartCount = $this->getCartCount();
        $cartTotal = $this->getCartTotal();

        return response()->json([
            'success' => true,
            'message' => 'Sepet güncellendi',
            'cart_count' => $cartCount,
            'cart_total' => $cartTotal
        ]);
    }

    /**
     * Sepeti temizle
     */
    public function clear()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        Cart::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })->delete();

        return response()->json([
            'success' => true,
            'message' => 'Sepet temizlendi'
        ]);
    }

    /**
     * Sepet ürünlerini getir
     */
    private function getCartItems()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        return Cart::getUserCart($userId, $sessionId);
    }

    /**
     * Sepet toplamını getir
     */
    private function getCartTotal()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        return Cart::getCartTotal($userId, $sessionId);
    }

    /**
     * Sepet ürün sayısını getir
     */
    private function getCartCount()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        return Cart::getCartCount($userId, $sessionId);
    }

    /**
     * Sepet sayısını API olarak döndür
     */
    public function getCount()
    {
        $count = $this->getCartCount();
        
        return response()->json([
            'count' => $count
        ]);
    }
}
