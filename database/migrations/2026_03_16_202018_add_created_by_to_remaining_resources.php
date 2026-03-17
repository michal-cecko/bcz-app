<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tables with team_id — place created_by after team_id
        $teamTables = [
            'sport_categories', 'exercise_categories', 'events',
            'payments', 'memberships', 'team_payouts', 'team_subscriptions',
        ];

        foreach ($teamTables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignUuid('created_by')->nullable()->after('team_id')->constrained('users')->nullOnDelete();
            });
        }

        // Tables without team_id — place created_by after id
        $globalTables = [
            'teams', 'pages', 'menus', 'sponsors', 'event_categories',
            'disciplines', 'athlete_categories', 'faq_categories',
            'subscription_plans', 'settings',
        ];

        foreach ($globalTables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignUuid('created_by')->nullable()->after('id')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'sport_categories', 'exercise_categories', 'events',
            'payments', 'memberships', 'team_payouts', 'team_subscriptions',
            'teams', 'pages', 'menus', 'sponsors', 'event_categories',
            'disciplines', 'athlete_categories', 'faq_categories',
            'subscription_plans', 'settings',
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropConstrainedForeignId('created_by');
            });
        }
    }
};
