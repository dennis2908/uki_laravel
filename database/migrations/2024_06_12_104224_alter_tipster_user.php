<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tipster_user', function (Blueprint $table) {
            DB::statement("ALTER TABLE tipster_user ADD UNIQUE (id,username)");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tipster_user', function (Blueprint $table) {
            DB::statement("ALTER TABLE tipster_user ADD UNIQUE (id,username)");
        });
    }
};
