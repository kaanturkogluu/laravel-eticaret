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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('product_code'); // Ürün kodu (backup için)
            $table->string('product_name'); // Ürün adı (backup için)
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Sipariş anındaki fiyat
            $table->decimal('unit_price_tl', 10, 2)->nullable(); // TL karşılığı
            $table->decimal('total_price', 10, 2); // quantity * unit_price
            $table->decimal('total_price_tl', 10, 2)->nullable(); // TL karşılığı
            $table->string('currency', 3)->default('TRY');
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
