<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table){
            $table->decimal('over_under_handicap', 8, 4)->after('big_bet_price');

            $table->dropColumn('over_under_handicap1');
            $table->dropColumn('over_under_handicap2');
        });

        Schema::table('tipster_home_team', function (Blueprint $table){
            $table->decimal('handicap', 8, 4)->after('odds');

            $table->dropColumn('handicap1');
            $table->dropColumn('handicap2');
        });

        Schema::table('tipster_away_team', function (Blueprint $table){
            $table->decimal('handicap', 8, 4)->after('odds');

            $table->dropColumn('handicap1');
            $table->dropColumn('handicap2');
        });
    }

    public function down(): void
    {
        Schema::table('tipster_match_bet', function (Blueprint $table){
            $table->dropColumn('over_under_handicap');

            $table->integer('over_under_handicap1')->after('big_bet_price');
            $table->integer('over_under_handicap2')->after('over_under_handicap1');
        });

        Schema::table('tipster_home_team', function (Blueprint $table){
            $table->dropColumn('handicap');

            $table->integer('handicap1')->after('odds');
            $table->integer('handicap2')->after('handicap1');
        });

        Schema::table('tipster_away_team', function (Blueprint $table){
            $table->dropColumn('handicap');

            $table->integer('handicap1')->after('odds');
            $table->integer('handicap2')->after('handicap1');
        });
    }
};
