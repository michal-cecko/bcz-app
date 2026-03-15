<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Timetable entries
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->foreignUuid('competition_detail_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Competition rounds
        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->foreignUuid('competition_detail_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Registration fees
        Schema::table('registration_fees', function (Blueprint $table) {
            $table->foreignUuid('competition_detail_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Pivot: competition_athlete_category
        Schema::table('competition_athlete_category', function (Blueprint $table) {
            $table->foreignUuid('competition_detail_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Pivot: competition_discipline
        Schema::table('competition_discipline', function (Blueprint $table) {
            $table->foreignUuid('competition_detail_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        // Pivot: competition_judges
        Schema::table('competition_judges', function (Blueprint $table) {
            $table->foreignUuid('competition_detail_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('timetable_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_detail_id');
        });

        Schema::table('competition_rounds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_detail_id');
        });

        Schema::table('registration_fees', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_detail_id');
        });

        Schema::table('competition_athlete_category', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_detail_id');
        });

        Schema::table('competition_discipline', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_detail_id');
        });

        Schema::table('competition_judges', function (Blueprint $table) {
            $table->dropConstrainedForeignId('competition_detail_id');
        });
    }
};
