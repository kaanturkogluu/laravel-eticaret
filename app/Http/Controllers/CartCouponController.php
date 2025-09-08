<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartCouponController extends Controller
{
    /**
     * Kupon uygula
     */
    public function apply(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::byCode($couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Geçersiz kupon kodu.'
            ]);
        }

        // Kullanıcı bilgilerini al
        $userId = auth()->id();
        $sessionId = Session::getId();

        // Sepet toplamını hesapla
        $cartTotal = Cart::getCartTotalInTry($userId, $sessionId);

        if ($cartTotal <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Sepetinizde ürün bulunmuyor.'
            ]);
        }

        // Kupon geçerliliğini kontrol et
        $validation = $coupon->canBeUsedFor($cartTotal, $userId, $sessionId);

        if (!$validation['valid']) {
            return response()->json([
                'success' => false,
                'message' => $validation['message']
            ]);
        }

        // Kuponu session'a kaydet
        Session::put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'name' => $coupon->name,
            'discount_amount' => $validation['discount'],
            'free_shipping' => $coupon->free_shipping
        ]);

        return response()->json([
            'success' => true,
            'message' => $validation['message'],
            'coupon' => [
                'code' => $coupon->code,
                'name' => $coupon->name,
                'discount_amount' => $validation['discount'],
                'formatted_discount' => number_format($validation['discount'], 2) . ' TL',
                'free_shipping' => $coupon->free_shipping
            ],
            'cart_total' => $cartTotal,
            'discount_amount' => $validation['discount'],
            'final_total' => $cartTotal - $validation['discount']
        ]);
    }

    /**
     * Kupon kaldır
     */
    public function remove()
    {
        Session::forget('applied_coupon');

        $userId = auth()->id();
        $sessionId = Session::getId();
        $cartTotal = Cart::getCartTotalInTry($userId, $sessionId);

        return response()->json([
            'success' => true,
            'message' => 'Kupon kaldırıldı.',
            'cart_total' => $cartTotal,
            'discount_amount' => 0,
            'final_total' => $cartTotal
        ]);
    }

    /**
     * Uygulanan kupon bilgilerini getir
     */
    public function getAppliedCoupon()
    {
        $appliedCoupon = Session::get('applied_coupon');

        if (!$appliedCoupon) {
            return response()->json([
                'success' => false,
                'message' => 'Uygulanan kupon bulunamadı.'
            ]);
        }

        $userId = auth()->id();
        $sessionId = Session::getId();
        $cartTotal = Cart::getCartTotalInTry($userId, $sessionId);

        return response()->json([
            'success' => true,
            'coupon' => $appliedCoupon,
            'cart_total' => $cartTotal,
            'discount_amount' => $appliedCoupon['discount_amount'],
            'final_total' => $cartTotal - $appliedCoupon['discount_amount']
        ]);
    }

    /**
     * Kupon geçerliliğini kontrol et
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $couponCode = strtoupper(trim($request->coupon_code));
        $coupon = Coupon::byCode($couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'valid' => false,
                'message' => 'Geçersiz kupon kodu.'
            ]);
        }

        $userId = auth()->id();
        $sessionId = Session::getId();
        $cartTotal = Cart::getCartTotalInTry($userId, $sessionId);

        $validation = $coupon->canBeUsedFor($cartTotal, $userId, $sessionId);

        return response()->json([
            'valid' => $validation['valid'],
            'message' => $validation['message'],
            'discount_amount' => $validation['discount']
        ]);
    }
}
