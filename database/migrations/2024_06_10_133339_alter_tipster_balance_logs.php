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
        Schema::table('tipster_balance_logs', function (Blueprint $table) {
            Schema::rename('tipster_balance_logs', 'tipster_balance_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            Schema::rename('tipster_balance_log', 'tipster_balance_logs');
        });
    }
};
