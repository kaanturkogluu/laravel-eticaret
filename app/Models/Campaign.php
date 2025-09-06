<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'title', 'description', 'image_url', 'link_url', 'type',
        'is_active', 'sort_order', 'start_date', 'end_date'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Aktif kampanyaları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    /**
     * Banner tipindeki kampanyaları getir
     */
    public function scopeBanners($query)
    {
        return $query->where('type', 'banner');
    }

    /**
     * Kampanya tipindeki kampanyaları getir
     */
    public function scopeCampaigns($query)
    {
        return $query->where('type', 'campaign');
    }

    /**
     * Promosyon tipindeki kampanyaları getir
     */
    public function scopePromotions($query)
    {
        return $query->where('type', 'promotion');
    }

    /**
     * Sıralama
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}