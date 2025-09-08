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
        Schema::create('payment_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // PayU, Sipay, vb.
            $table->string('code')->unique(); // payu, sipay, vb.
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->json('config')->nullable(); // API anahtarları, URL'ler vb.
            $table->json('supported_currencies')->nullable(); // ['TRY', 'USD', 'EUR']
            $table->json('supported_payment_methods')->nullable(); // ['credit_card', 'bank_transfer', 'wallet']
            $table->decimal('min_amount', 10, 2)->nullable();
            $table->decimal('max_amount', 10, 2)->nullable();
            $table->decimal('commission_rate', 5, 4)->default(0); // Yüzde komisyon
            $table->decimal('commission_fixed', 10, 2)->default(0); // Sabit komisyon
            $table->boolean('test_mode')->default(true);
            $table->string('webhook_url')->nullable();
            $table->string('callback_url')->nullable();
            $table->string('return_url')->nullable();
            $table->string('cancel_url')->nullable();
            $table->timestamps();
            
            $table->index(['is_active', 'sort_order']);
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_providers');
    }
};
