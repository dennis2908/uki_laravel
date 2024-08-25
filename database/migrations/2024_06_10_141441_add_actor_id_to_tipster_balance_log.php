<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            $table->integer('actor_id')->default(0)->after('tipster_transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            $table->dropColumn('actor_id');
        });
    }
};
