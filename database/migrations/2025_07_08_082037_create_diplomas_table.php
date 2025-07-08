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
        Schema::create('diplomas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('curso_id');
            $table->unsignedBigInteger('persona_id');
            $table->date('fecha_expedicion');
            $table->string('path_pdf');
            $table->json('otros_datos')->nullable();
            $table->timestamps();

            $table->foreign('curso_id')->references('id')->on('cursos')->onDelete('cascade');
            $table->foreign('persona_id')->references('id')->on('personas')->onDelete('cascade');
            
            // Clave única compuesta para evitar diplomas duplicados
            $table->unique(['curso_id', 'persona_id'], 'diplomas_curso_persona_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diplomas');
    }
};
