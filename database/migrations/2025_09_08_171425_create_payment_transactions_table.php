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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_provider_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique(); // Bizim oluşturduğumuz ID
            $table->string('external_transaction_id')->nullable(); // Gateway'den gelen ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('TRY');
            $table->string('payment_method'); // credit_card, bank_transfer, wallet
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded', 'partially_refunded'])->default('pending');
            $table->json('gateway_response')->nullable(); // Gateway'den gelen yanıt
            $table->json('gateway_error')->nullable(); // Hata detayları
            $table->json('callback_data')->nullable(); // Callback verileri
            $table->json('webhook_data')->nullable(); // Webhook verileri
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('net_amount', 10, 2)->default(0);
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Ek bilgiler
            $table->timestamps();
            
            $table->index(['order_id', 'status']);
            $table->index(['payment_provider_id', 'status']);
            $table->index(['transaction_id']);
            $table->index(['external_transaction_id']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
