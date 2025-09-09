<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller
{
    /**
     * Kampanya listesi
     */
    public function index()
    {
        $campaigns = Campaign::active()
            ->whereIn('type', ['campaign', 'promotion'])
            ->ordered()
            ->paginate(12);

        return view('campaigns.index', compact('campaigns'));
    }

    /**
     * Kampanya detayı
     */
    public function show(Campaign $campaign)
    {
        // Kampanya aktif değilse 404 döndür
        if (!$campaign->is_active) {
            abort(404);
        }

        // Tarih kontrolü
        if ($campaign->start_date && $campaign->start_date > now()) {
            abort(404);
        }

        if ($campaign->end_date && $campaign->end_date < now()) {
            abort(404);
        }

        // İlgili kampanyalar
        $relatedCampaigns = Campaign::active()
            ->where('type', $campaign->type)
            ->where('id', '!=', $campaign->id)
            ->ordered()
            ->limit(4)
            ->get();

        return view('campaigns.show', compact('campaign', 'relatedCampaigns'));
    }

    /**
     * Banner'ları getir (AJAX)
     */
    public function getBanners()
    {
        $banners = Campaign::active()
            ->banners()
            ->ordered()
            ->limit(5)
            ->get();

        return response()->json($banners);
    }

    /**
     * Aktif kampanyaları getir (AJAX)
     */
    public function getActiveCampaigns()
    {
        $campaigns = Campaign::active()
            ->whereIn('type', ['campaign', 'promotion'])
            ->ordered()
            ->limit(6)
            ->get();

        return response()->json($campaigns);
    }
}
