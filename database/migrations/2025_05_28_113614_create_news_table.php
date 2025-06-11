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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 60)->unique();
            $table->text('contenido');
            $table->string('imagen')->nullable();

            // $table->foreignId('users_id')->constrained('users'); 
            // esto seria el uso de clave foranea con relacion a la tabla users

            // Si no quieres hacer la FK, al menos define el campo como unsignedBigInteger
            $table->unsignedBigInteger('autor')->nullable();
            $table->timestamp('fecha_publicacion');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
