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
        // Schema::table('tipster_match_bet', function (Blueprint $table) {
        //     DB::STATEMENT('ALTER TABLE tipster_match_bet MODIFY football_match_id VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('tipster_match_bet', function (Blueprint $table) {
        //     DB::STATEMENT('ALTER TABLE tipster_match_bet MODIFY football_match_id VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;');
        // });
    }
};
