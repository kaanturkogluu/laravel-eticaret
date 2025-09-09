<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CampaignController extends Controller
{
    /**
     * Kampanya listesi
     */
    public function index()
    {
        $campaigns = Campaign::ordered()->paginate(20);
        return view('admin.campaigns.index', compact('campaigns'));
    }

    /**
     * Yeni kampanya formu
     */
    public function create()
    {
        return view('admin.campaigns.create');
    }

    /**
     * Kampanya kaydet
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:banner,campaign,promotion',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0'
        ]);

        $data = $request->only([
            'title', 'description', 'type', 'link_url', 
            'start_date', 'end_date', 'sort_order', 'is_active',
            'discount_type', 'discount_value', 'minimum_amount', 'maximum_discount'
        ]);

        // Resim yükleme
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::slug($request->title) . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('campaigns', $filename, 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Varsayılan değerler
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        Campaign::create($data);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanya başarıyla oluşturuldu.');
    }

    /**
     * Kampanya düzenleme formu
     */
    public function edit(Campaign $campaign)
    {
        return view('admin.campaigns.edit', compact('campaign'));
    }

    /**
     * Kampanya güncelle
     */
    public function update(Request $request, Campaign $campaign)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:banner,campaign,promotion',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'link_url' => 'nullable|url',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'discount_type' => 'nullable|in:percentage,fixed',
            'discount_value' => 'nullable|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0'
        ]);

        $data = $request->only([
            'title', 'description', 'type', 'link_url', 
            'start_date', 'end_date', 'sort_order', 'is_active',
            'discount_type', 'discount_value', 'minimum_amount', 'maximum_discount'
        ]);

        // Resim yükleme
        if ($request->hasFile('image')) {
            // Eski resmi sil
            if ($campaign->image_url) {
                $oldImage = str_replace('/storage/', '', $campaign->image_url);
                Storage::disk('public')->delete($oldImage);
            }

            $image = $request->file('image');
            $filename = Str::slug($request->title) . '_' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('campaigns', $filename, 'public');
            $data['image_url'] = Storage::url($path);
        }

        // Varsayılan değerler
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        $campaign->update($data);

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanya başarıyla güncellendi.');
    }

    /**
     * Kampanya sil
     */
    public function destroy(Campaign $campaign)
    {
        // Resmi sil
        if ($campaign->image_url) {
            $oldImage = str_replace('/storage/', '', $campaign->image_url);
            Storage::disk('public')->delete($oldImage);
        }

        $campaign->delete();

        return redirect()->route('admin.campaigns.index')
            ->with('success', 'Kampanya başarıyla silindi.');
    }

    /**
     * Kampanya durumunu değiştir
     */
    public function toggleStatus(Campaign $campaign)
    {
        $campaign->update(['is_active' => !$campaign->is_active]);

        $status = $campaign->is_active ? 'aktif' : 'pasif';
        return redirect()->back()
            ->with('success', "Kampanya {$status} hale getirildi.");
    }

    /**
     * Kampanya sıralamasını güncelle
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'campaigns' => 'required|array',
            'campaigns.*.id' => 'required|exists:campaigns,id',
            'campaigns.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->campaigns as $campaignData) {
            Campaign::where('id', $campaignData['id'])
                ->update(['sort_order' => $campaignData['sort_order']]);
        }

        return response()->json(['success' => true]);
    }
}
