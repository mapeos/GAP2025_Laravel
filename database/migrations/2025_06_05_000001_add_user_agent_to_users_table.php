<?php
// Migración para agregar el campo user_agent a la tabla users
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Agrega el campo user_agent para registrar el agente de usuario (navegador, app, etc.)
            $table->string('user_agent')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('user_agent');
        });
    }
};
