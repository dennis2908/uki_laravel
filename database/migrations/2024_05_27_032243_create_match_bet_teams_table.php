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
        Schema::create('tipster_match_bet', function (Blueprint $table) {
            $table->id();
            $table->string("table_name");
            $table->string("table_id");
            $table->enum('type', ['home', 'away'])->default("away");
            $table->double("odds");
            $table->double("over");
            $table->double("under");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_bet_teams');
    }
};
