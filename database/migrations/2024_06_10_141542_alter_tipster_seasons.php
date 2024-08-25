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
        Schema::table('tipster_seasons', function (Blueprint $table) {
            Schema::rename('tipster_seasons', 'tipster_season');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_seasons', function (Blueprint $table) {
            Schema::rename('tipster_seasons', 'tipster_season');
        });
    }
};
