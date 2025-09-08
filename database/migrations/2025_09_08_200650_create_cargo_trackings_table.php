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
        Schema::create('cargo_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('cargo_company_id')->constrained()->onDelete('cascade');
            $table->string('tracking_number');
            $table->enum('status', [
                'created', 'picked_up', 'in_transit', 
                'out_for_delivery', 'delivered', 'exception', 'returned'
            ])->default('created');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->timestamp('event_date');
            $table->boolean('is_delivered')->default(false);
            $table->timestamps();
            
            $table->index(['order_id', 'event_date']);
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cargo_trackings');
    }
};
