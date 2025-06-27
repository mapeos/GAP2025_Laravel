<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('device_id'); // Unique ID per device
            $table->string('device_token')->nullable(); // This stores the Firebase token
            $table->string('device_name')->nullable();
            $table->string('device_os')->nullable();
            $table->string('app_version')->nullable();
            $table->json('extra_data')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'device_id']);
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
