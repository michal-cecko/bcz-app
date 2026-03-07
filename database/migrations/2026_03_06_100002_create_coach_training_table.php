<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_training', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('training_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('main');
            $table->timestamps();

            $table->unique(['user_id', 'training_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_training');
    }
};
