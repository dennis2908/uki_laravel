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
        Schema::table('tipster_configs', function (Blueprint $table) {
            Schema::rename('tipster_configs', 'tipster_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_configs', function (Blueprint $table) {
            Schema::rename('tipster_config', 'tipster_configs');
        });
    }
};
