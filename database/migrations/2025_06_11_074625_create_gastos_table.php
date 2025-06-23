<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gastos', function (Blueprint $table) {
            $table->id(); // id
            $table->unsignedBigInteger('id_persona'); // id persona
            $table->string('concepto');
            $table->date('fecha');
            $table->decimal('importe', 10, 2);
            $table->boolean('pagado')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
