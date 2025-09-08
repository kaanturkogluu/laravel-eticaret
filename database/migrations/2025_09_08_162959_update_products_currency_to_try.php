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
        // Tüm ürünlerin para birimini USD'den TRY'ye çevir
        \App\Models\Product::where('doviz', 'USD')->update(['doviz' => 'TRY']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Geri al: TRY'den USD'ye çevir
        \App\Models\Product::where('doviz', 'TRY')->update(['doviz' => 'USD']);
    }
};
