<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    /**
     * Kupon listesi
     */
    public function index(Request $request)
    {
        $query = Coupon::withCount('usages');

        // Arama
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Durum filtresi
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'expired':
                    $query->where('expires_at', '<', now());
                    break;
                case 'limit_reached':
                    $query->whereRaw('used_count >= usage_limit');
                    break;
            }
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Kupon oluşturma formu
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Kupon kaydet
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'free_shipping' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);

        // Yüzde indirimde maksimum %100 olmalı
        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return redirect()->back()
                ->withErrors(['value' => 'Yüzde indirim %100\'den fazla olamaz.'])
                ->withInput();
        }

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon başarıyla oluşturuldu.');
    }

    /**
     * Kupon düzenleme formu
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Kupon güncelle
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed_amount',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'free_shipping' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        $data['code'] = strtoupper($data['code']);

        // Yüzde indirimde maksimum %100 olmalı
        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return redirect()->back()
                ->withErrors(['value' => 'Yüzde indirim %100\'den fazla olamaz.'])
                ->withInput();
        }

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon başarıyla güncellendi.');
    }

    /**
     * Kupon sil
     */
    public function destroy(Coupon $coupon)
    {
        // Kullanılmış kuponları silme
        if ($coupon->used_count > 0) {
            return redirect()->back()
                ->with('error', 'Kullanılmış kuponlar silinemez.');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Kupon başarıyla silindi.');
    }

    /**
     * Kupon kullanım geçmişi
     */
    public function usageHistory(Coupon $coupon)
    {
        $usages = $coupon->usages()
            ->with(['user', 'order'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.coupons.usage-history', compact('coupon', 'usages'));
    }

    /**
     * Kupon durumunu değiştir
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        $status = $coupon->is_active ? 'aktif' : 'pasif';
        
        return redirect()->back()
            ->with('success', "Kupon {$status} hale getirildi.");
    }

    /**
     * Kupon kodu oluştur
     */
    public function generateCode(Request $request)
    {
        $length = $request->get('length', 8);
        $code = Coupon::generateCode($length);
        
        return response()->json(['code' => $code]);
    }
}
