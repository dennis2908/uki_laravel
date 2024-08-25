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
        Schema::create('tipster_transaction_cancels', function (Blueprint $table) {
            $table->id();
            $table->integer("user_id");
            $table->integer('tipster_transaction_id');
            $table->datetime('cancel_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipster_transaction_cancels');
    }
};
