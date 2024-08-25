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
        Schema::table('tipster_home_teams', function (Blueprint $table) {
            $table->dropColumn('table_id');
            $table->dropColumn('table_name');
            $table->string('football_team_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_home_teams', function (Blueprint $table) {
            $table->dropColumn('table_id');
            $table->dropColumn('table_name');
            $table->string('football_team_id');
        });
    }
};