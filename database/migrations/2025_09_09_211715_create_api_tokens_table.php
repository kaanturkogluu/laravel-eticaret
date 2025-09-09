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
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Token adı (örn: "Mobile App", "Web App")
            $table->string('token', 64)->unique(); // API token
            $table->text('abilities')->nullable(); // Token yetkileri (JSON)
            $table->timestamp('last_used_at')->nullable(); // Son kullanım zamanı
            $table->timestamp('expires_at')->nullable(); // Token sona erme zamanı
            $table->boolean('is_active')->default(true); // Token aktif mi
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
