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
        Schema::create('motivos_cita', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('duracion_minutos')->default(30);
            $table->string('categoria')->default('General');
            $table->enum('tipo_sistema', ['academico', 'medico', 'general'])->default('academico');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index(['tipo_sistema', 'activo']);
            $table->index('categoria');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motivos_cita');
    }
}; 