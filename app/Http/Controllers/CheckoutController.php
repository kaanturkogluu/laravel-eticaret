<?php

namespace App\Http\Controllers;

use App\Mail\OrderConfirmationMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\PaymentProvider;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    /**
     * Checkout sayfası
     */
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();
        $cartTotalInTry = Cart::getCartTotalInTry(Auth::id(), Session::getId());
        $cartCount = $this->getCartCount();

        if ($cartCount === 0) {
            return redirect()->route('cart.index')->with('error', 'Sepetinizde ürün bulunmuyor.');
        }

        // Kupon bilgilerini al
        $appliedCoupon = Cart::getAppliedCoupon();
        $discountAmount = Cart::getDiscountAmount();
        $finalTotal = Cart::getCartTotalWithDiscount(Auth::id(), Session::getId());
        $hasFreeShipping = Cart::hasFreeShipping();

        $user = Auth::user();

        return view('checkout.index', compact(
            'cartItems', 'cartTotal', 'cartTotalInTry', 'cartCount', 'user',
            'appliedCoupon', 'discountAmount', 'finalTotal', 'hasFreeShipping'
        ));
    }

    /**
     * Siparişi tamamla
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'shipping_address' => 'required|string',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_district' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:10',
            'billing_address' => 'nullable|string',
            'billing_city' => 'nullable|string|max:100',
            'billing_district' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'payment_method' => 'required|string|in:credit_card,bank_transfer,wallet,cash_on_delivery',
            'payment_provider' => 'nullable|string|exists:payment_providers,code',
            'installment' => 'nullable|integer|min:1|max:12'
        ]);

        $cartItems = $this->getCartItems();
        $cartTotal = $this->getCartTotal();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Sepetinizde ürün bulunmuyor.');
        }

        // Kupon bilgilerini al
        $appliedCoupon = Cart::getAppliedCoupon();
        $discountAmount = Cart::getDiscountAmount();
        $finalTotal = Cart::getCartTotalWithDiscount(Auth::id(), Session::getId());
        $hasFreeShipping = Cart::hasFreeShipping();

        // Stok kontrolü
        foreach ($cartItems as $cartItem) {
            if ($cartItem->product->miktar < $cartItem->quantity) {
                return back()->with('error', "{$cartItem->product->ad} ürünü için yeterli stok bulunmuyor. Mevcut stok: {$cartItem->product->miktar}");
            }
        }

        DB::beginTransaction();
        try {
            // Sipariş oluştur
            // TL karşılığı hesapla
            $cartTotalInTry = Cart::getCartTotalInTry(Auth::id(), Session::getId());
            
            // Kupon bilgilerini hazırla
            $couponId = null;
            $couponCode = null;
            $discountAmountTl = 0;
            
            if ($appliedCoupon) {
                $couponId = $appliedCoupon['id'];
                $couponCode = $appliedCoupon['code'];
                $discountAmountTl = $discountAmount; // Zaten TL cinsinden hesaplanmış
            }
            
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'status' => 'pending',
                'subtotal' => $cartTotal,
                'subtotal_tl' => $cartTotalInTry,
                'shipping_cost' => $hasFreeShipping ? 0 : 0, // Ücretsiz kargo
                'shipping_cost_tl' => $hasFreeShipping ? 0 : 0,
                'total' => $finalTotal,
                'total_tl' => $finalTotal,
                'currency' => $cartItems->first() ? $cartItems->first()->product->doviz : 'TRY',
                'coupon_id' => $couponId,
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'discount_amount_tl' => $discountAmountTl,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_district' => $request->shipping_district,
                'shipping_postal_code' => $request->shipping_postal_code,
                'billing_address' => $request->billing_address ?: $request->shipping_address,
                'billing_city' => $request->billing_city ?: $request->shipping_city,
                'billing_district' => $request->billing_district ?: $request->shipping_district,
                'billing_postal_code' => $request->billing_postal_code ?: $request->shipping_postal_code,
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'notes' => $request->notes,
            ]);

            // Sipariş kalemlerini oluştur
            foreach ($cartItems as $cartItem) {
                $unitPrice = $cartItem->product->best_price;
                $totalPrice = $cartItem->quantity * $unitPrice;
                
                // TL karşılığı hesapla
                $unitPriceTl = $cartItem->product->doviz === 'TRY' ? $unitPrice : $unitPrice * \App\Models\Currency::getRate($cartItem->product->doviz);
                $totalPriceTl = $cartItem->quantity * $unitPriceTl;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_code' => $cartItem->product->kod,
                    'product_name' => $cartItem->product->ad,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $unitPrice,
                    'unit_price_tl' => $unitPriceTl,
                    'total_price' => $totalPrice,
                    'total_price_tl' => $totalPriceTl,
                    'currency' => $cartItem->product->doviz,
                ]);

                // Stoktan düş
                $cartItem->product->decrement('miktar', $cartItem->quantity);
            }

            // Kupon kullanım kaydı oluştur
            if ($appliedCoupon) {
                $coupon = Coupon::find($appliedCoupon['id']);
                if ($coupon) {
                    $coupon->use(
                        Auth::id(),
                        $order->id,
                        Session::getId(),
                        $discountAmount,
                        $cartTotalInTry
                    );
                }
            }

            // Sepeti temizle
            $this->clearCart();
            
            // Kupon session'ını temizle
            Cart::removeCoupon();

            // Ödeme işlemi
            if ($request->payment_method === 'cash_on_delivery') {
                // Kapıda ödeme - otomatik başarılı
                $order->update([
                    'payment_status' => 'pending',
                    'payment_reference' => 'COD-' . time() . '-' . rand(1000, 9999),
                    'status' => 'processing'
                ]);
                
                DB::commit();
                
                // Sipariş onay maili gönder
                try {
                    $user = Auth::user();
                    Mail::to($user->email)->send(new OrderConfirmationMail($order, $user));
                } catch (\Exception $e) {
                    // Mail gönderimi başarısız olsa bile sipariş işlemi devam etsin
                    \Log::error('Order confirmation mail failed: ' . $e->getMessage());
                }
                
                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Siparişiniz başarıyla oluşturuldu! Kapıda ödeme ile teslim edilecektir.');
            } else {
                // Online ödeme
                $paymentProvider = PaymentProvider::where('code', $request->payment_provider)
                    ->where('is_active', true)
                    ->first();
                
                if (!$paymentProvider) {
                    DB::rollback();
                    return back()->with('error', 'Seçilen ödeme sağlayıcısı bulunamadı.');
                }
                
                try {
                    $service = PaymentServiceFactory::create($paymentProvider);
                    
                    $paymentData = [
                        'payment_method' => $request->payment_method,
                        'installment' => $request->installment ?? 1,
                        'metadata' => [
                            'user_agent' => $request->userAgent(),
                            'ip_address' => $request->ip(),
                            'referer' => $request->header('referer')
                        ]
                    ];
                    
                    $result = $service->initiatePayment($order, $paymentData);
                    
                    if ($result['success']) {
                        DB::commit();
                        
                        // Payment URL varsa yönlendir
                        if (isset($result['data']['payment_url'])) {
                            return redirect($result['data']['payment_url']);
                        } else {
                            return redirect()->route('orders.show', $order->id)
                                ->with('success', 'Ödeme işlemi başlatıldı. Lütfen ödeme sayfasını kontrol edin.');
                        }
                    } else {
                        DB::rollback();
                        return back()->with('error', 'Ödeme işlemi başlatılamadı: ' . $result['message']);
                    }
                } catch (\Exception $e) {
                    DB::rollback();
                    return back()->with('error', 'Ödeme işlemi sırasında hata oluştu: ' . $e->getMessage());
                }
            }

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Sipariş oluşturulurken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Sepet ürünlerini getir
     */
    private function getCartItems()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        return Cart::with('product.images')
            ->where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->get();
    }

    /**
     * Sepet toplamını getir
     */
    private function getCartTotal()
    {
        $cartItems = $this->getCartItems();
        return $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->best_price;
        });
    }

    /**
     * Sepet ürün sayısını getir
     */
    private function getCartCount()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $query = Cart::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        return $query->sum('quantity');
    }

    /**
     * Sepeti temizle
     */
    private function clearCart()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $query = Cart::query();
        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_id', $sessionId);
        }

        $query->delete();
    }
}
