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
        Schema::create('category_profits', function (Blueprint $table) {
            $table->id();
            $table->string('category_name')->unique(); // Kategori adı (örn: "Laptop", "Telefon")
            $table->boolean('is_active')->default(true); // Aktif/pasif durumu
            $table->integer('profit_type')->default(1); // 0: Kar yok, 1: Yüzde kar, 2: Sabit kar
            $table->decimal('profit_value', 10, 2)->default(0); // Kar değeri
            $table->text('description')->nullable(); // Açıklama
            $table->timestamps();
            
            $table->index(['category_name', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_profits');
    }
};
