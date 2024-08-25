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
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            $table->integer("tipster_season_id");
            $table->decimal('bet_price', 12,2);
            $table->decimal('big_bet_price', 12,2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            $table->integer("tipster_season_id");
            $table->decimal('bet_price', 12,2);
            $table->decimal('big_bet_price', 12,2);
        });
    }
};
