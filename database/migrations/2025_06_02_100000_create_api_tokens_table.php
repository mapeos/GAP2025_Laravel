<?php
// Migración para la tabla api_tokens
// Esta tabla permite almacenar tokens independientes para usuarios y/o dispositivos.
// Puede ser utilizada por otros equipos para gestionar accesos API externos.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->nullable()->constrained('devices')->onDelete('cascade');
            $table->string('token')->unique(); // Token de acceso
            $table->string('type')->nullable(); // Ej: 'mobile', 'web', 'external', etc.
            $table->timestamp('expires_at')->nullable(); // Fecha de expiración opcional
            $table->json('meta')->nullable(); // Información adicional opcional
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_tokens');
    }
};
