<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ürünlerin %30'unu USD para birimine çevir
        $totalProducts = \App\Models\Product::count();
        $usdCount = (int)($totalProducts * 0.3);
        
        \App\Models\Product::inRandomOrder()
            ->limit($usdCount)
            ->update(['doviz' => 'USD']);
        
        // Kalan ürünlerin %20'sini EUR para birimine çevir
        $eurCount = (int)($totalProducts * 0.2);
        
        \App\Models\Product::where('doviz', 'TRY')
            ->inRandomOrder()
            ->limit($eurCount)
            ->update(['doviz' => 'EUR']);
        
        // Kalan ürünlerin %10'unu GBP para birimine çevir
        $gbpCount = (int)($totalProducts * 0.1);
        
        \App\Models\Product::where('doviz', 'TRY')
            ->inRandomOrder()
            ->limit($gbpCount)
            ->update(['doviz' => 'GBP']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Tüm ürünleri tekrar TRY para birimine çevir
        \App\Models\Product::whereIn('doviz', ['USD', 'EUR', 'GBP'])
            ->update(['doviz' => 'TRY']);
    }
};
