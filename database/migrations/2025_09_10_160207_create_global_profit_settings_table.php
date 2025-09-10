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
        Schema::create('global_profit_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false)->comment('Genel kar sistemi aktif mi');
            $table->integer('profit_type')->default(1)->comment('0: Kar yok, 1: Yüzde kar, 2: Sabit kar');
            $table->decimal('profit_value', 10, 2)->default(0)->comment('Genel kar değeri (yüzde veya sabit tutar)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_profit_settings');
    }
};
