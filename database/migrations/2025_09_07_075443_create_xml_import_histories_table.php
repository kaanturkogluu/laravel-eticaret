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
        Schema::create('xml_import_histories', function (Blueprint $table) {
            $table->id();
            $table->string('filename'); // Dosya adı
            $table->string('file_path')->nullable(); // Dosya yolu
            $table->bigInteger('file_size')->default(0); // Dosya boyutu
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending'); // İşlem durumu
            $table->integer('imported_count')->default(0); // Yeni eklenen ürün sayısı
            $table->integer('updated_count')->default(0); // Güncellenen ürün sayısı
            $table->integer('skipped_count')->default(0); // Atlanan ürün sayısı
            $table->integer('error_count')->default(0); // Hata alan ürün sayısı
            $table->integer('total_processed')->default(0); // Toplam işlenen ürün sayısı
            $table->timestamp('started_at')->nullable(); // İşlem başlangıç zamanı
            $table->timestamp('completed_at')->nullable(); // İşlem bitiş zamanı
            $table->text('error_message')->nullable(); // Hata mesajı
            $table->timestamps();
            
            $table->index(['status', 'created_at']);
            $table->index('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('xml_import_histories');
    }
};
