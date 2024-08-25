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
        Schema::create('tipster_transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('tipster_match_bet_id');
            $table->enum('type', ['home', 'away'])->default("away");
            $table->enum('bet_type', ['big', 'normal'])->default("normal");
            $table->datetime('place_bet_time');
            $table->decimal('win_prize', 12,3);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipster_transactions');
    }
};
