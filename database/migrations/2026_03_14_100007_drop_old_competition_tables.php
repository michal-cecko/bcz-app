<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop competition_reports first (references competitions)
        Schema::dropIfExists('competition_reports');

        // Drop competition_registrations (references competitions)
        Schema::dropIfExists('competition_registrations');

        // Now drop old competition_id FKs from child tables
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_id');
        });

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_id');
        });

        Schema::table('registration_fees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_id');
        });

        // Drop old pivot unique constraints (using actual constraint names) and competition_id columns
        Schema::table('competition_athlete_category', function (Blueprint $table) {
            $table->dropUnique('comp_ath_cat_unique');
            $table->dropConstrainedForeignId('competition_id');
        });

        Schema::table('competition_discipline', function (Blueprint $table) {
            $table->dropUnique('competition_discipline_competition_id_discipline_id_unique');
            $table->dropConstrainedForeignId('competition_id');
        });

        Schema::table('competition_judges', function (Blueprint $table) {
            $table->dropUnique('comp_disc_judge_unique');
            $table->dropConstrainedForeignId('competition_id');
        });

        // Clean up orphaned rows (fresh start — no data migration)
        DB::table('timetable_entries')->whereNull('competition_detail_id')->delete();
        DB::table('competition_rounds')->whereNull('competition_detail_id')->delete();
        DB::table('registration_fees')->whereNull('competition_detail_id')->delete();
        DB::table('competition_athlete_category')->whereNull('competition_detail_id')->delete();
        DB::table('competition_discipline')->whereNull('competition_detail_id')->delete();
        DB::table('competition_judges')->whereNull('competition_detail_id')->delete();

        // Make competition_detail_id NOT NULL now that competition_id is gone
        $tables = [
            'timetable_entries',
            'competition_rounds',
            'registration_fees',
            'competition_athlete_category',
            'competition_discipline',
            'competition_judges',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->uuid('competition_detail_id')->nullable(false)->change();
            });
        }

        // Add new unique constraints on competition_detail_id
        Schema::table('competition_athlete_category', function (Blueprint $table) {
            $table->unique(['competition_detail_id', 'athlete_category_id']);
        });

        Schema::table('competition_discipline', function (Blueprint $table) {
            $table->unique(['competition_detail_id', 'discipline_id']);
        });

        Schema::table('competition_judges', function (Blueprint $table) {
            $table->unique(['competition_detail_id', 'discipline_id', 'user_id']);
        });

        // Finally drop the competitions table
        Schema::dropIfExists('competitions');
    }

    public function down(): void
    {
        // Recreating the old tables is not supported — fresh start decision
    }
};
