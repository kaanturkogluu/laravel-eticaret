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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('kod')->unique(); // XML'deki Kod alanı
            $table->string('ad'); // XML'deki Ad alanı
            $table->integer('miktar')->default(0); // XML'deki Miktar alanı
            $table->decimal('fiyat_sk', 10, 2)->nullable(); // XML'deki Fiyat_SK
            $table->decimal('fiyat_bayi', 10, 2)->nullable(); // XML'deki Fiyat_Bayi
            $table->decimal('fiyat_ozel', 10, 2)->nullable(); // XML'deki Fiyat_Ozel
            $table->string('doviz', 3)->default('USD'); // XML'deki Doviz
            $table->string('marka')->nullable(); // XML'deki Marka
            $table->string('kategori')->nullable(); // XML'deki Kategori
            $table->string('ana_grup_kod')->nullable(); // XML'deki AnaGrup_Kod
            $table->string('ana_grup_ad')->nullable(); // XML'deki AnaGrup_Ad
            $table->string('alt_grup_kod')->nullable(); // XML'deki AltGrup_Kod
            $table->string('alt_grup_ad')->nullable(); // XML'deki AltGrup_Ad
            $table->string('ana_resim')->nullable(); // XML'deki AnaResim
            $table->string('barkod')->nullable(); // XML'deki Barkod
            $table->text('aciklama')->nullable(); // XML'deki Aciklama
            $table->text('detay')->nullable(); // XML'deki Detay
            $table->decimal('desi', 8, 2)->default(0); // XML'deki Desi
            $table->integer('kdv')->default(20); // XML'deki Kdv
            $table->boolean('is_active')->default(true); // Aktif/pasif durumu
            $table->timestamp('last_updated')->nullable(); // Son güncelleme zamanı
            $table->timestamps();
            
            $table->index(['is_active', 'miktar']);
            $table->index('marka');
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
