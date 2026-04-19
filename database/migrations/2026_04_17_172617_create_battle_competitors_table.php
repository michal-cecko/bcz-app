<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('battle_competitors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('battle_id')->constrained()->cascadeOnDelete();
            $table->string('side', 1);
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('user_name');
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['battle_id', 'side', 'user_id']);
            $table->index(['battle_id', 'side']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('battle_competitors');
    }
};
