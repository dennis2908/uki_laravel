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
        Schema::table('tipster_user_balances', function (Blueprint $table) {
            $table->decimal('balance', 12,3)->change();
            $table->decimal('open_bet', 12,3)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_user_balances', function (Blueprint $table) {
            $table->decimal('balance', 12,3)->change();
            $table->decimal('open_bet', 12,3)->change();
        });
    }
};
