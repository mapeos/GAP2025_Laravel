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
        Schema::create('tratamientos_medicos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('costo', 10, 2);
            $table->integer('duracion_minutos')->default(60);
            $table->unsignedBigInteger('especialidad_id');
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->foreign('especialidad_id')->references('id')->on('especialidades_medicas')->onDelete('cascade');
            $table->unique(['nombre', 'especialidad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos_medicos');
    }
}; 