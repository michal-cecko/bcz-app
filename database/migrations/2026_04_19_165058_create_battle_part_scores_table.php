<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battle_part_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('battle_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('round_part_id')->constrained('round_parts')->cascadeOnDelete();
            $table->string('side', 1);
            $table->decimal('score', 8, 2)->nullable();
            $table->timestamps();

            $table->unique(['battle_id', 'round_part_id', 'side']);
            $table->index(['battle_id', 'side']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battle_part_scores');
    }
};
