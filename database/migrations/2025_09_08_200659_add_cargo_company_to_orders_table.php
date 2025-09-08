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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('cargo_company_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cargo_tracking_number')->nullable();
            $table->timestamp('cargo_created_at')->nullable();
            $table->timestamp('cargo_picked_up_at')->nullable();
            $table->timestamp('cargo_delivered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['cargo_company_id']);
            $table->dropColumn([
                'cargo_company_id', 
                'cargo_tracking_number',
                'cargo_created_at',
                'cargo_picked_up_at',
                'cargo_delivered_at'
            ]);
        });
    }
};
