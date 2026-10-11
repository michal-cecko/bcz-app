<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("CREATE UNIQUE INDEX training_member_current_unique ON training_registrations (training_id, member_id) WHERE member_id IS NOT NULL AND status <> 'cancelled'");
        DB::statement("CREATE UNIQUE INDEX membership_member_season_unique ON memberships (member_id, team_id, team_season_id) WHERE member_id IS NOT NULL AND team_season_id IS NOT NULL AND status IN ('active', 'pending')");
        DB::statement("CREATE UNIQUE INDEX membership_member_no_season_unique ON memberships (member_id, team_id) WHERE member_id IS NOT NULL AND team_season_id IS NULL AND status IN ('active', 'pending')");
    }

    public function down(): void
    {
        DB::statement('DROP INDEX training_member_current_unique');
        DB::statement('DROP INDEX membership_member_season_unique');
        DB::statement('DROP INDEX membership_member_no_season_unique');
    }
};
