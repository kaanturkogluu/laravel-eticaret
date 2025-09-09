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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable()->after('end_date');
            $table->decimal('discount_value', 10, 2)->nullable()->after('discount_type');
            $table->decimal('minimum_amount', 10, 2)->nullable()->after('discount_value');
            $table->decimal('maximum_discount', 10, 2)->nullable()->after('minimum_amount');
            $table->json('applicable_products')->nullable()->after('maximum_discount');
            $table->json('applicable_categories')->nullable()->after('applicable_products');
            $table->json('excluded_products')->nullable()->after('applicable_categories');
            $table->json('excluded_categories')->nullable()->after('excluded_products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'discount_type',
                'discount_value',
                'minimum_amount',
                'maximum_discount',
                'applicable_products',
                'applicable_categories',
                'excluded_products',
                'excluded_categories'
            ]);
        });
    }
};
