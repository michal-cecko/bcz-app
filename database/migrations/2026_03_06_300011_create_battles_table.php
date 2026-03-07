<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('competition_round_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('athlete_category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('bracket_position');
            $table->json('competitor_a_id')->nullable();
            $table->json('competitor_b_id')->nullable();
            $table->json('winner_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
