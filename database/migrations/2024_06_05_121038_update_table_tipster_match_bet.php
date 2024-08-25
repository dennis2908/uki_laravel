<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            DB::STATEMENT("ALTER TABLE `tipster_match_bet` CHANGE `over` `overs` DECIMAL(12,2)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            DB::STATEMENT("ALTER TABLE `tipster_match_bet` CHANGE `over` `overs` DECIMAL(12,2)");
        });
    }
};
