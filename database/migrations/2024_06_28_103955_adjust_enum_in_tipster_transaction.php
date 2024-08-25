<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipster_transaction', function (Blueprint $table) {
            $table->enum('type', ['home', 'away', 'over', 'under'])->change();
            $table->integer('status')->after('win_prize')->default(1)->comment('1 => waiting for match, 2 => match in progress, 3 => win the prize, 4 => lose the prize, 5 => cancel bet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_transaction', function (Blueprint $table) {
            $table->enum('type', ['home', 'away'])->change();
            $table->dropColumn('status');
        });
    }
};
