<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryProfit extends Model
{
    protected $fillable = [
        'category_name',
        'is_active',
        'profit_type',
        'profit_value',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'profit_type' => 'integer',
        'profit_value' => 'decimal:2'
    ];

    /**
     * Aktif kategori kar ayarlarını getir
     */
    public static function getActiveProfits()
    {
        return static::where('is_active', true)->get();
    }

    /**
     * Belirli bir kategori için kar ayarını getir
     */
    public static function getProfitForCategory(string $categoryName)
    {
        return static::where('category_name', $categoryName)
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * Kar hesaplama yap
     */
    public function calculateProfit(float $basePrice): float
    {
        if (!$this->is_active || $this->profit_type == 0) {
            return $basePrice;
        }

        switch ($this->profit_type) {
            case 1: // Yüzde kar
                return $basePrice * (1 + ($this->profit_value / 100));
            case 2: // Sabit kar
                return $basePrice + $this->profit_value;
            default:
                return $basePrice;
        }
    }

    /**
     * Kar türü açıklaması
     */
    public function getProfitTypeDescriptionAttribute(): string
    {
        switch ($this->profit_type) {
            case 0:
                return 'Kar Yok';
            case 1:
                return 'Yüzde Kar';
            case 2:
                return 'Sabit Kar';
            default:
                return 'Bilinmiyor';
        }
    }

    /**
     * Kar değeri formatı
     */
    public function getFormattedProfitValueAttribute(): string
    {
        if ($this->profit_type == 1) {
            return '%' . number_format($this->profit_value, 1);
        } else {
            return number_format($this->profit_value, 2) . ' TL';
        }
    }
}
