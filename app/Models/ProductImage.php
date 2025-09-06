<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id', 'urun_kodu', 'resim_url', 'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * Ürün ilişkisi
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
