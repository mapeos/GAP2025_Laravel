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
            $table->unsignedBigInteger('facultativo_id')->nullable()->after('profesor_id');
            $table->foreign('facultativo_id')->references('id')->on('facultativos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_citas', function (Blueprint $table) {
            $table->dropForeign(['facultativo_id']);
            $table->dropColumn('facultativo_id');
        });
    }
};
