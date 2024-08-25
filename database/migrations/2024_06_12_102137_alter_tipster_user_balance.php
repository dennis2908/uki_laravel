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
        Schema::table('tipster_user_balance', function (Blueprint $table) {
            Schema::rename('tipster_user_balance', 'tipster_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_user', function (Blueprint $table) {
            Schema::rename('tipster_user', 'tipster_user_balance');
        });
    }
};
