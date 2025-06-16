<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->string('temario_path')->nullable()->after('estado'); // Agrega la columna para la ruta del archivo
        });
    }

    public function down()
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn('temario_path'); // Elimina la columna si se revierte la migración
        });
    }
};
