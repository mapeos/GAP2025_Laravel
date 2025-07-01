<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id_origen');
            $table->unsignedBigInteger('user_id_destino');
            $table->text('mensaje');
            $table->boolean('leido')->default(false);
            $table->timestamps();

            $table->foreign('user_id_origen')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id_destino')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
