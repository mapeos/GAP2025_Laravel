<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'device_id']);
            $table->foreignId('user_id')->nullable()->change();
            $table->unique(['user_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'device_id']);
            $table->foreignId('user_id')->nullable(false)->change();
            $table->unique(['user_id', 'device_id']);
        });
    }
};
