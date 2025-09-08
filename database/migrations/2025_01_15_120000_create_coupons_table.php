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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['percentage', 'fixed_amount']); // Yüzde veya sabit tutar
            $table->decimal('value', 10, 2); // İndirim değeri
            $table->decimal('minimum_amount', 10, 2)->nullable(); // Minimum sepet tutarı
            $table->decimal('maximum_discount', 10, 2)->nullable(); // Maksimum indirim tutarı
            $table->integer('usage_limit')->nullable(); // Toplam kullanım limiti
            $table->integer('usage_limit_per_user')->nullable(); // Kullanıcı başına kullanım limiti
            $table->integer('used_count')->default(0); // Kullanım sayısı
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable(); // Başlangıç tarihi
            $table->timestamp('expires_at')->nullable(); // Bitiş tarihi
            $table->json('applicable_products')->nullable(); // Belirli ürünlere uygulanabilir
            $table->json('applicable_categories')->nullable(); // Belirli kategorilere uygulanabilir
            $table->json('excluded_products')->nullable(); // Hariç tutulacak ürünler
            $table->json('excluded_categories')->nullable(); // Hariç tutulacak kategoriler
            $table->boolean('free_shipping')->default(false); // Ücretsiz kargo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
