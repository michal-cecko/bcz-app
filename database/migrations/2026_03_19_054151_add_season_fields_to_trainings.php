<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->foreignUuid('team_season_id')->nullable()->after('team_id')
                ->constrained('team_seasons')->nullOnDelete();
            $table->boolean('is_recurring_across_seasons')->default(true)->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('trainings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_season_id');
            $table->dropColumn('is_recurring_across_seasons');
        });
    }
};
