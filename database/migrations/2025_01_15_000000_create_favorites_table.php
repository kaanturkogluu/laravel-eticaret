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
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('product_kod'); // Ürün kodu
            $table->timestamps();

            // Aynı kullanıcı aynı ürünü birden fazla kez favorilere ekleyemez
            $table->unique(['user_id', 'product_kod']);
            
            // Index'ler
            $table->index('user_id');
            $table->index('product_kod');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
