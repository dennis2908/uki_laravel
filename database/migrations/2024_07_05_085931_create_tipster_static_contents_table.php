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
        Schema::create('tipster_static_contents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->string('slug');
            $table->boolean('is_active')->nullable()->default(1)->comment('1 : active, 2 : not-active');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipster_static_contents');
    }
};
