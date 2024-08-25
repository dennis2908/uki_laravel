<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            $table->dropColumn('odds');

            $table->renameColumn('overs', 'odds_over');
            $table->renameColumn('under', 'odds_under');
            $table->renameColumn('over_under_handicap', 'handicap');
        });
    }

    public function down(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table) {
            $table->decimal('odds', 12, 3)->after('id');

            $table->renameColumn('odds_over', 'overs');
            $table->renameColumn('odds_under', 'under');
            $table->renameColumn('handicap', 'over_under_handicap');
        });
    }
};
