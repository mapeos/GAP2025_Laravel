<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Eliminar la clave foránea y el índice único
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'device_id']);
        });
        Schema::table('devices', function (Blueprint $table) {
            // Hacer user_id nullable
            $table->foreignId('user_id')->nullable()->change();
            // Volver a crear la clave foránea
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // Volver a crear el índice único
            $table->unique(['user_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'device_id']);
        });
        Schema::table('devices', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'device_id']);
        });
    }
};
