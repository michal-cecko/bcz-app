<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_waitlist', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('training_id')->constrained('trainings')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['training_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_waitlist');
    }
};
