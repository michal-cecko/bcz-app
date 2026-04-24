<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('judges', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->json('biography')->nullable();
            $table->json('disciplines')->nullable();
            $table->date('date_started_judging')->nullable();
            $table->json('socials')->nullable();
            $table->timestamps();
        });

        Schema::table('competition_judges', function (Blueprint $table) {
            $table->dropUnique(['competition_detail_id', 'discipline_id', 'user_id']);
            $table->dropConstrainedForeignId('user_id');
            $table->foreignUuid('judge_id')->after('discipline_id')->constrained()->cascadeOnDelete();
            $table->unique(['competition_detail_id', 'discipline_id', 'judge_id'], 'comp_detail_disc_judge_unique');
        });

        Schema::table('certifications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            $table->uuidMorphs('certifiable');
        });

        Schema::dropIfExists('judge_profiles');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('judge_profile_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('judge_profile_approved_at')->nullable();
        });

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

        Schema::table('certifications', function (Blueprint $table) {
            $table->dropMorphs('certifiable');
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('competition_judges', function (Blueprint $table) {
            $table->dropUnique('comp_detail_disc_judge_unique');
            $table->dropConstrainedForeignId('judge_id');
            $table->foreignUuid('user_id')->after('discipline_id')->constrained()->cascadeOnDelete();
            $table->unique(['competition_detail_id', 'discipline_id', 'user_id']);
        });

        Schema::dropIfExists('judges');
    }
};
