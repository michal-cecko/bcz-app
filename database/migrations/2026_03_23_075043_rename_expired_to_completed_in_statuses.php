<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('memberships')
            ->where('status', 'expired')
            ->update(['status' => 'completed']);

        DB::table('team_subscriptions')
            ->where('status', 'expired')
            ->update(['status' => 'completed']);
    }

    public function down(): void
    {
        DB::table('memberships')
            ->where('status', 'completed')
            ->update(['status' => 'expired']);

        DB::table('team_subscriptions')
            ->where('status', 'completed')
            ->update(['status' => 'expired']);
    }
};
