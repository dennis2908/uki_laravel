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
        Schema::create('tipster_balance_logs', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->enum('type', ['debit', 'credit'])->default("debit");
            $table->enum('action_type', ["bet", "win", "bonus", "adjustment"])->default("bet");
            $table->integer('tipster_transaction_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipster_balance_logs');
    }
};
