<?php
// database/migrations/2025_06_13_rename_toker_columns_in_devices_table.php

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
        Schema::table('devices', function (Blueprint $table) {
            $table->renameColumn('fcm_toker', 'fcm_token');
            $table->renameColumn('device_toker', 'device_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->renameColumn('fcm_token', 'fcm_toker');
            $table->renameColumn('device_token', 'device_toker');
        });
    }
};
