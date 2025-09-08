<?php

namespace App\Http\Controllers;

use App\Mail\OrderStatusUpdateMail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Sipariş detayı
     */
    public function show($id)
    {
        $order = Order::with(['items.product.images', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('orders.show', compact('order'));
    }

    /**
     * Siparişi iptal et
     */
    public function cancel($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $oldStatus = $order->status;
        
        if ($order->cancel()) {
            // Sipariş durumu güncelleme maili gönder
            try {
                $user = $order->user;
                Mail::to($user->email)->send(new OrderStatusUpdateMail($order, $user, $oldStatus, 'cancelled'));
            } catch (\Exception $e) {
                \Log::error('Order status update mail failed: ' . $e->getMessage());
            }
            
            return back()->with('success', 'Siparişiniz iptal edildi.');
        }

        return back()->with('error', 'Bu sipariş iptal edilemez.');
    }

    /**
     * Siparişi teslim edildi olarak işaretle
     */
    public function markAsDelivered($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $oldStatus = $order->status;
        
        if ($order->markAsDelivered()) {
            // Sipariş durumu güncelleme maili gönder
            try {
                $user = $order->user;
                Mail::to($user->email)->send(new OrderStatusUpdateMail($order, $user, $oldStatus, 'delivered'));
            } catch (\Exception $e) {
                \Log::error('Order status update mail failed: ' . $e->getMessage());
            }
            
            return back()->with('success', 'Siparişiniz teslim edildi olarak işaretlendi.');
        }

        return back()->with('error', 'Bu sipariş teslim edildi olarak işaretlenemez.');
    }
}
