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
        Schema::table('tipster_away_teams', function (Blueprint $table) {
            $table->string('table_name');
            $table->string('table_id');
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_away_teams', function (Blueprint $table) {
            $table->string('table_name');
            $table->string('table_id');
            $table->dropColumn('name');
        });
    }
};
