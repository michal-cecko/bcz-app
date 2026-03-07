<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_category_sport_category', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('exercise_category_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('sport_category_id')->constrained()->cascadeOnDelete();

            $table->unique(['exercise_category_id', 'sport_category_id'], 'exercise_cat_sport_cat_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_category_sport_category');
    }
};
