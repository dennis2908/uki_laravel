<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            $table->decimal('balance', 12, 2)->after('action_type');
        });
    }

    public function down(): void
    {
        Schema::table('tipster_balance_log', function (Blueprint $table) {
            $table->dropColumn('balance');
        });
    }
};
