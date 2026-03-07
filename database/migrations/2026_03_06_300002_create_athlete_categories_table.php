<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('athlete_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->json('name');
            $table->uuid('parent_id')->nullable();
            $table->json('description')->nullable();
            $table->string('gender')->nullable();
            $table->decimal('min_weight', 6, 2)->nullable();
            $table->decimal('max_weight', 6, 2)->nullable();
            $table->unsignedSmallInteger('min_age')->nullable();
            $table->unsignedSmallInteger('max_age')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('athlete_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('athlete_categories')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('athlete_categories');
    }
};
