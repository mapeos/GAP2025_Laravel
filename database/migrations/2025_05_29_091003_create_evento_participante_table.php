<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Crea la tabla pivote que gestiona la relación entre eventos y
     * usuarios, registrando el rol y estado de asistencia de cada participante.
     */
    public function up(): void
    {
        Schema::create('evento_participante', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('evento_id')
                  ->constrained('eventos')
                  ->onDelete('cascade');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Campos específicos
            $table->string('rol', 50)->default('asistente');
            $table->string('estado_asistencia', 20)->default('pendiente');
            $table->text('notas')->nullable();

            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index('rol');
            $table->index('estado_asistencia');
            $table->index('status');

            // Índice único para evitar duplicados
            $table->unique(['evento_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento_participante');
    }
};
