<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_athlete_category', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('athlete_category_id')->constrained()->cascadeOnDelete();
            $table->unique(['competition_id', 'athlete_category_id'], 'comp_ath_cat_unique');
        });

        Schema::create('competition_discipline', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('discipline_id')->constrained()->cascadeOnDelete();
            $table->unique(['competition_id', 'discipline_id']);
        });

        Schema::create('competition_judges', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('discipline_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->unique(['competition_id', 'discipline_id', 'user_id'], 'comp_disc_judge_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_judges');
        Schema::dropIfExists('competition_discipline');
        Schema::dropIfExists('competition_athlete_category');
    }
};
