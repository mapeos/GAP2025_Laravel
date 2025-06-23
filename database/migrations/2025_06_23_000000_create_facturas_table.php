<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('pago_id');
            $table->string('producto');
            $table->decimal('importe', 10, 2);
            $table->date('fecha');
            $table->string('estado')->default('pagada');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pago_id')->references('id_pago')->on('pagos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
