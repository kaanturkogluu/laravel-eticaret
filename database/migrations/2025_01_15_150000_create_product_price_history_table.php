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
        Schema::create('product_price_history', function (Blueprint $table) {
            $table->id();
            $table->string('product_kod');
            $table->decimal('old_price_sk', 10, 2)->nullable();
            $table->decimal('old_price_bayi', 10, 2)->nullable();
            $table->decimal('old_price_ozel', 10, 2)->nullable();
            $table->decimal('new_price_sk', 10, 2)->nullable();
            $table->decimal('new_price_bayi', 10, 2)->nullable();
            $table->decimal('new_price_ozel', 10, 2)->nullable();
            $table->string('currency', 3)->default('TRY');
            $table->decimal('price_difference', 10, 2)->nullable(); // Fiyat farkı
            $table->decimal('discount_percentage', 5, 2)->nullable(); // İndirim yüzdesi
            $table->boolean('is_discount')->default(false); // İndirim var mı?
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['product_kod', 'changed_at']);
            $table->index(['is_discount', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_price_history');
    }
};
