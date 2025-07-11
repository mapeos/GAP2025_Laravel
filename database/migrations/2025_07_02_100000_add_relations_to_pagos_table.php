<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->unsignedBigInteger('persona_id')->nullable()->after('id_gasto');
            $table->unsignedBigInteger('curso_id')->nullable()->after('persona_id');
            $table->unsignedBigInteger('payment_method_id')->nullable()->after('curso_id');

            $table->foreign('persona_id')->references('id')->on('personas')->nullOnDelete();
            $table->foreign('curso_id')->references('id')->on('cursos')->nullOnDelete();
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['persona_id']);
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['payment_method_id']);
            $table->dropColumn(['persona_id', 'curso_id', 'payment_method_id']);
        });
    }
};
