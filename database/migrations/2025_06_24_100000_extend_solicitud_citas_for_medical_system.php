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
        Schema::table('solicitud_citas', function (Blueprint $table) {
            // Campo para distinguir entre citas académicas y médicas
            $table->enum('tipo_sistema', ['academico', 'medico'])->default('academico')->after('estado');
            
            // Campos específicos para citas médicas
            $table->unsignedBigInteger('especialidad_id')->nullable()->after('tipo_sistema');
            $table->unsignedBigInteger('tratamiento_id')->nullable()->after('especialidad_id');
            $table->text('sintomas')->nullable()->after('tratamiento_id');
            $table->text('diagnostico')->nullable()->after('sintomas');
            $table->decimal('costo', 10, 2)->nullable()->after('diagnostico');
            $table->integer('duracion_minutos')->default(60)->after('costo');
            $table->text('observaciones_medicas')->nullable()->after('duracion_minutos');
            $table->date('fecha_proxima_cita')->nullable()->after('observaciones_medicas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_citas', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_sistema',
                'especialidad_id',
                'tratamiento_id',
                'sintomas',
                'diagnostico',
                'costo',
                'duracion_minutos',
                'observaciones_medicas',
                'fecha_proxima_cita'
            ]);
        });
    }
}; 