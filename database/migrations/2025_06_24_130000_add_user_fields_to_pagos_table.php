<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->string('nombre')->nullable()->after('id_gasto');
            $table->string('email')->nullable()->after('nombre');
            $table->string('curso')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['nombre', 'email', 'curso']);
        });
    }
};
