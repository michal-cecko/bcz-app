<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->dropColumn(['competitor_a_id', 'competitor_b_id', 'winner_id']);
        });

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropColumn(['advance_count', 'battle_size']);
        });
    }

    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            $table->json('competitor_a_id')->nullable();
            $table->json('competitor_b_id')->nullable();
            $table->json('winner_id')->nullable();
        });

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->unsignedSmallInteger('advance_count')->nullable();
            $table->unsignedSmallInteger('battle_size')->nullable();
        });
    }
};
