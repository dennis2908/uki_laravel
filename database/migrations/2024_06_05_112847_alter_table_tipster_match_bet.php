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
            $table->decimal('odds', 12,2)->change();
            $table->decimal('over', 12,2)->change();
            $table->decimal('under', 12,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            $table->decimal('odds', 12,2)->change();
            $table->decimal('over', 12,2)->change();
            $table->decimal('under', 12,2)->change();
        });
    }
};
