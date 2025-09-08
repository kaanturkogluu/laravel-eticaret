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
            $table->decimal('subtotal_tl', 10, 2)->nullable()->after('subtotal');
            $table->decimal('shipping_cost_tl', 10, 2)->default(0)->after('shipping_cost');
            $table->decimal('total_tl', 10, 2)->nullable()->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['subtotal_tl', 'shipping_cost_tl', 'total_tl']);
        });
    }
};
