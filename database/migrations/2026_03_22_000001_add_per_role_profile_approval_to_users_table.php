<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('coach_profile_approved_at')->nullable()->after('public_profile_approved_at');
            $table->timestamp('athlete_profile_approved_at')->nullable()->after('coach_profile_approved_at');
            $table->timestamp('judge_profile_approved_at')->nullable()->after('athlete_profile_approved_at');
        });

        // Migrate existing approval data to role-specific columns
        DB::statement('
            UPDATE users
            SET coach_profile_approved_at = public_profile_approved_at
            WHERE has_public_profile = true
              AND public_profile_approved_at IS NOT NULL
              AND id IN (SELECT user_id FROM coach_profiles)
        ');

        DB::statement('
            UPDATE users
            SET athlete_profile_approved_at = public_profile_approved_at
            WHERE has_public_profile = true
              AND public_profile_approved_at IS NOT NULL
              AND id IN (SELECT user_id FROM athlete_profiles)
        ');
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['coach_profile_approved_at', 'athlete_profile_approved_at', 'judge_profile_approved_at']);
        });
    }
};
