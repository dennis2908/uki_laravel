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
            $table->integer('over_under_handicap1');
            $table->integer('over_under_handicap2');
            $table->decimal('odds', 12,3)->change();
            $table->decimal('under', 12,3)->change();
            $table->decimal('overs', 12,3)->change();
            $table->decimal('bet_price', 12,3)->change();
            $table->decimal('big_bet_price', 12,3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            $table->integer('over_under_handicap1');
            $table->integer('over_under_handicap2');
            $table->decimal('odds', 12,3)->change();
            $table->decimal('under', 12,3)->change();
            $table->decimal('overs', 12,3)->change();
            $table->decimal('bet_price', 12,3)->change();
            $table->decimal('big_bet_price', 12,3)->change();
        });
    }
};
