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
        Schema::create('tipster_away_teams', function (Blueprint $table) {
            $table->id();
            $table->integer('tsr_match_bet_id');
            $table->string('name');
            $table->decimal('odds', 12,3);
            $table->integer('handicap1');
            $table->integer('handicap2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipster_away_teams');
    }
};
