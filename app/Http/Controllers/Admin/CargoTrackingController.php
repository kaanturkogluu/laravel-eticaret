<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CargoTracking;
use App\Models\CargoCompany;
use App\Models\Order;
use App\Mail\CargoNotificationMail;
use App\Mail\CargoStatusUpdateMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CargoTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cargoTrackings = CargoTracking::with(['order', 'cargoCompany'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.cargo-trackings.index', compact('cargoTrackings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $orders = Order::where('status', 'processing')
            ->whereNull('cargo_company_id')
            ->with('user')
            ->get();
        $cargoCompanies = CargoCompany::where('is_active', true)->get();
        
        return view('admin.cargo-trackings.create', compact('orders', 'cargoCompanies'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'cargo_company_id' => 'required|exists:cargo_companies,id',
            'tracking_number' => 'required|string|max:100',
            'status' => 'required|in:created,picked_up,in_transit,out_for_delivery,delivered,exception,returned',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date'
        ]);

        DB::beginTransaction();
        try {
            // Kargo takip kaydı oluştur
            $cargoTracking = CargoTracking::create([
                'order_id' => $request->order_id,
                'cargo_company_id' => $request->cargo_company_id,
                'tracking_number' => $request->tracking_number,
                'status' => $request->status,
                'description' => $request->description,
                'location' => $request->location,
                'event_date' => $request->event_date,
                'is_delivered' => $request->status === 'delivered'
            ]);

            // Siparişi güncelle
            $order = Order::find($request->order_id);
            $order->update([
                'cargo_company_id' => $request->cargo_company_id,
                'cargo_tracking_number' => $request->tracking_number,
                'tracking_number' => $request->tracking_number,
                'status' => $request->status === 'delivered' ? 'delivered' : 'shipped',
                'shipped_at' => $request->status === 'delivered' ? $request->event_date : now(),
                'delivered_at' => $request->status === 'delivered' ? $request->event_date : null,
                'cargo_created_at' => $request->status === 'created' ? $request->event_date : null,
                'cargo_picked_up_at' => $request->status === 'picked_up' ? $request->event_date : null,
                'cargo_delivered_at' => $request->status === 'delivered' ? $request->event_date : null
            ]);

            DB::commit();

            // Kargo bilgilendirme maili gönder
            try {
                $user = $order->user;
                Mail::to($user->email)->send(new CargoNotificationMail($order, $cargoTracking));
            } catch (\Exception $e) {
                // Mail gönderimi başarısız olsa bile kargo işlemi devam etsin
                \Log::error('Cargo notification mail failed: ' . $e->getMessage());
            }

            return redirect()->route('admin.cargo-trackings.index')
                ->with('success', 'Kargo takip kaydı başarıyla oluşturuldu ve müşteriye bilgilendirme maili gönderildi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Kargo takip kaydı oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CargoTracking $cargoTracking)
    {
        $cargoTracking->load(['order.items.product', 'cargoCompany']);
        $trackingHistory = CargoTracking::where('order_id', $cargoTracking->order_id)
            ->orderBy('event_date', 'asc')
            ->get();
            
        return view('admin.cargo-trackings.show', compact('cargoTracking', 'trackingHistory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CargoTracking $cargoTracking)
    {
        $cargoCompanies = CargoCompany::where('is_active', true)->get();
        return view('admin.cargo-trackings.edit', compact('cargoTracking', 'cargoCompanies'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CargoTracking $cargoTracking)
    {
        $request->validate([
            'cargo_company_id' => 'required|exists:cargo_companies,id',
            'tracking_number' => 'required|string|max:100',
            'status' => 'required|in:created,picked_up,in_transit,out_for_delivery,delivered,exception,returned',
            'description' => 'nullable|string',
            'location' => 'nullable|string|max:255',
            'event_date' => 'required|date'
        ]);

        $oldStatus = $cargoTracking->status;
        $newStatus = $request->status;

        $cargoTracking->update([
            'cargo_company_id' => $request->cargo_company_id,
            'tracking_number' => $request->tracking_number,
            'status' => $newStatus,
            'description' => $request->description,
            'location' => $request->location,
            'event_date' => $request->event_date,
            'is_delivered' => $newStatus === 'delivered'
        ]);

        // Sipariş durumunu güncelle
        $order = $cargoTracking->order;
        if ($newStatus === 'delivered') {
            $order->update([
                'status' => 'delivered',
                'delivered_at' => $request->event_date,
                'cargo_delivered_at' => $request->event_date
            ]);
        } elseif (in_array($newStatus, ['picked_up', 'in_transit', 'out_for_delivery'])) {
            $order->update([
                'status' => 'shipped',
                'cargo_picked_up_at' => $newStatus === 'picked_up' ? $request->event_date : $order->cargo_picked_up_at
            ]);
        }

        // Durum değiştiyse mail gönder
        if ($oldStatus !== $newStatus) {
            try {
                $user = $order->user;
                Mail::to($user->email)->send(new CargoStatusUpdateMail($order, $cargoTracking, $oldStatus, $newStatus));
            } catch (\Exception $e) {
                // Mail gönderimi başarısız olsa bile güncelleme devam etsin
                \Log::error('Cargo status update mail failed: ' . $e->getMessage());
            }
        }

        return redirect()->route('admin.cargo-trackings.index')
            ->with('success', 'Kargo takip kaydı başarıyla güncellendi' . ($oldStatus !== $newStatus ? ' ve müşteriye bilgilendirme maili gönderildi' : '') . '.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CargoTracking $cargoTracking)
    {
        $cargoTracking->delete();

        return redirect()->route('admin.cargo-trackings.index')
            ->with('success', 'Kargo takip kaydı başarıyla silindi.');
    }

    /**
     * Sipariş için kargo takip geçmişi
     */
    public function orderTracking($orderId)
    {
        $order = Order::with(['items.product', 'cargoCompany'])->findOrFail($orderId);
        $trackingHistory = CargoTracking::where('order_id', $orderId)
            ->orderBy('event_date', 'asc')
            ->get();
            
        return view('admin.cargo-trackings.order-tracking', compact('order', 'trackingHistory'));
    }

    /**
     * Takip numarası ile arama
     */
    public function search(Request $request)
    {
        $trackingNumber = $request->get('tracking_number');
        
        if (!$trackingNumber) {
            return redirect()->route('admin.cargo-trackings.index')
                ->with('error', 'Takip numarası giriniz.');
        }

        $cargoTrackings = CargoTracking::where('tracking_number', 'like', "%{$trackingNumber}%")
            ->with(['order', 'cargoCompany'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.cargo-trackings.index', compact('cargoTrackings', 'trackingNumber'));
    }
}
