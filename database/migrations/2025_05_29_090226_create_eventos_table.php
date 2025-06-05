<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration

{
    /**
     * Run the migrations.
     *
     * Esta migración crea la tabla eventos que almacena todos los eventos
     * del calendario, incluyendo clases, exámenes, reuniones, etc.
     * cada evento está asociado a un tipo de evento y puede tener múltiples
     * participantes.
     */
    public function up():void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            //ubicacion del evento (aula, sala, etc.)
            $table->string('ubicacion', 200)->nullable();
            //url para eventos virtuales (reuniones online, etc.)
            $table->string('url_virtual', 255)->nullable();
            // Relación con tipo de evento
            $table->foreignID('tipo_evento_id')
                ->constrained('tipos_evento')
                ->onDelete('restrict');
            // Relación con usuario creador
            $table->foreignID('creado_por')
                ->constrained('users')
                ->onDelete('restrict');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha_inicio');
            $table->index('fecha_fin');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
