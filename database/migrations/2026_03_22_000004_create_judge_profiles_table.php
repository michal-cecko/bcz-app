<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judge_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->json('biography')->nullable();
            $table->json('disciplines')->nullable();
            $table->date('date_started_judging')->nullable();
            $table->json('draft_data')->nullable();
            $table->string('draft_status')->nullable();
            $table->text('draft_rejection_reason')->nullable();
            $table->timestamp('draft_submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('judge_profiles');
    }
};
