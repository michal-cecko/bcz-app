<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->boolean('continuous_membership')->default(false)->after('is_active');
        });

        // Backfill: any pivot row whose user has a Membership on that team
        DB::statement('
            UPDATE team_user
            SET continuous_membership = true
            WHERE EXISTS (
                SELECT 1 FROM memberships
                WHERE memberships.user_id = team_user.user_id
                  AND memberships.team_id = team_user.team_id
            )
        ');

        // Backfill: any pivot row whose user has a TrainingRegistration for a
        // MEMBERSHIP_REQUIRED training on that team
        DB::statement("
            UPDATE team_user
            SET continuous_membership = true
            WHERE EXISTS (
                SELECT 1
                FROM training_registrations
                INNER JOIN trainings ON trainings.id = training_registrations.training_id
                WHERE training_registrations.user_id = team_user.user_id
                  AND trainings.team_id = team_user.team_id
                  AND trainings.pricing_type = 'membership_required'
            )
        ");
    }

    public function down(): void
    {
        Schema::table('team_user', function (Blueprint $table) {
            $table->dropColumn('continuous_membership');
        });
    }
};
