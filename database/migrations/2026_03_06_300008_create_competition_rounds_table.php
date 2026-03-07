<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competition_rounds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('competition_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('athlete_category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('round_number');
            $table->string('name');
            $table->string('advancement_type');
            $table->unsignedSmallInteger('advance_count')->nullable();
            $table->unsignedSmallInteger('battle_size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competition_rounds');
    }
};
