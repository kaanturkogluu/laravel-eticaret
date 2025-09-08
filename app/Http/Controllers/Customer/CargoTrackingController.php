<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CargoTracking;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CargoTrackingController extends Controller
{
    /**
     * Takip numarası ile kargo sorgulama
     */
    public function track(Request $request)
    {
        $trackingNumber = $request->get('tracking_number');
        $cargoTracking = null;
        $trackingHistory = collect();

        if ($trackingNumber) {
            $cargoTracking = CargoTracking::where('tracking_number', $trackingNumber)
                ->with(['order', 'cargoCompany'])
                ->first();

            if ($cargoTracking) {
                $trackingHistory = CargoTracking::where('order_id', $cargoTracking->order_id)
                    ->orderBy('event_date', 'asc')
                    ->get();
            }
        }

        return view('customer.cargo-tracking.track', compact('trackingNumber', 'cargoTracking', 'trackingHistory'));
    }

    /**
     * Kullanıcının siparişlerinin kargo takibi
     */
    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->whereNotNull('cargo_company_id')
            ->with(['cargoCompany', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.cargo-tracking.my-orders', compact('orders'));
    }

    /**
     * Sipariş detayı ve kargo takibi
     */
    public function orderTracking($orderId)
    {
        $order = Order::where('id', $orderId)
            ->where('user_id', Auth::id())
            ->with(['cargoCompany', 'items.product'])
            ->firstOrFail();

        $trackingHistory = CargoTracking::where('order_id', $orderId)
            ->orderBy('event_date', 'asc')
            ->get();

        return view('customer.cargo-tracking.order-tracking', compact('order', 'trackingHistory'));
    }
}
