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
            UPDATE team_user tu
            SET continuous_membership = true
            WHERE EXISTS (
                SELECT 1 FROM memberships m
                WHERE m.user_id = tu.user_id AND m.team_id = tu.team_id
            )
        ');

        // Backfill: any pivot row whose user has a TrainingRegistration for a
        // MEMBERSHIP_REQUIRED training on that team
        DB::statement("
            UPDATE team_user tu
            SET continuous_membership = true
            WHERE EXISTS (
                SELECT 1
                FROM training_registrations tr
                INNER JOIN trainings t ON t.id = tr.training_id
                WHERE tr.user_id = tu.user_id
                  AND t.team_id = tu.team_id
                  AND t.pricing_type = 'membership_required'
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
