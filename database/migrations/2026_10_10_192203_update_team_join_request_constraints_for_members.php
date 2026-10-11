<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('team_join_requests', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'email']);
            $table->unique(['team_id', 'member_id']);
        });
        DB::statement('CREATE UNIQUE INDEX team_join_requests_legacy_email_unique ON team_join_requests (team_id, email) WHERE member_id IS NULL');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX IF EXISTS team_join_requests_legacy_email_unique');
        Schema::table('team_join_requests', function (Blueprint $table) {
            $table->dropUnique(['team_id', 'member_id']);
            $table->unique(['team_id', 'email']);
        });
    }
};
