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
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable(); // Misafir kullanıcılar için
            $table->decimal('discount_amount', 10, 2); // Uygulanan indirim tutarı
            $table->decimal('order_total', 10, 2); // Sipariş toplamı
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['coupon_id', 'user_id']);
            $table->index(['coupon_id', 'session_id']);
            $table->index('order_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon_usages');
    }
};
