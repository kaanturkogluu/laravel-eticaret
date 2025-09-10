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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('profit_type', 1, 0)->default(0)->comment('0: Kar yok, 1: Yüzde kar, 2: Sabit kar');
            $table->decimal('profit_value', 10, 2)->default(0)->comment('Kar değeri (yüzde veya sabit tutar)');
            $table->boolean('profit_enabled')->default(false)->comment('Kar hesaplaması aktif mi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['profit_type', 'profit_value', 'profit_enabled']);
        });
    }
};
