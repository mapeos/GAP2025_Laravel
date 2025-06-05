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
        Schema::create('news_has_categorias', function (Blueprint $table) {
            $table->unsignedBigInteger('news_id');
            $table->unsignedBigInteger('categorias_id');
            $table->timestamps();

            //Definir ls clave primaria compuesta
            $table->primary(['news_id', 'categorias_id']);

            //Definir los indices
            $table->index('news_id');
            $table->index('categorias_id');

            //Definir las claves foraneas
            $table->foreign('news_id')
                ->references('id')->on('news') 
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('categorias_id')
                ->references('id')->on('categorias') 
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_has_categorias');
    }
};
