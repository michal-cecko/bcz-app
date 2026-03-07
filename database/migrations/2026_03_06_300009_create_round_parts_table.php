<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('round_parts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('competition_round_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('round_parts');
    }
};
