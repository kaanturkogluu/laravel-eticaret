<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = [
        'code', 'name', 'rate', 'last_updated'
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'last_updated' => 'datetime',
    ];

    /**
     * Para birimi kodu ile kur getir
     */
    public static function getRate($code)
    {
        $currency = static::where('code', strtoupper($code))->first();
        return $currency ? $currency->rate : 1; // TRY için 1 döndür
    }

    /**
     * Güncel kurları getir
     */
    public static function getCurrentRates()
    {
        return static::orderBy('code')->get();
    }

    /**
     * Belirli para birimlerinin kurlarını getir
     */
    public static function getRatesFor($codes = ['USD', 'EUR', 'GBP'])
    {
        return static::whereIn('code', $codes)->get()->keyBy('code');
    }

    /**
     * Formatlanmış kur
     */
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate, 2);
    }
}
