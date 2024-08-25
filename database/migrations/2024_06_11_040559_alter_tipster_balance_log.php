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
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            $table->integer('tipster_transaction_cancel_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            $table->integer('tipster_transaction_cancel_id');
        });
    }
};
