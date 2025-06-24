<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('id_pago'); // id pago
            $table->unsignedBigInteger('id_gasto'); // id gasto
            $table->decimal('importe', 10, 2);
            $table->string('concepto');
            $table->date('fecha');
            $table->boolean('pendiente')->default(true);
            $table->timestamps();

            // Relación con gastos (clave foránea)
            $table->foreign('id_gasto')->references('id')->on('gastos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
