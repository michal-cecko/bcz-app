<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('membership_allow_yearly', 'membership_allow_seasonal');
            $table->renameColumn('membership_fee_amount_yearly', 'membership_fee_amount_seasonal');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->unsignedTinyInteger('membership_season_start_month')->nullable()->after('membership_fee_amount_seasonal');
            $table->unsignedTinyInteger('membership_season_end_month')->nullable()->after('membership_season_start_month');
        });

        DB::table('memberships')
            ->where('period', 'yearly')
            ->update(['period' => 'seasonal']);
    }

    public function down(): void
    {
        DB::table('memberships')
            ->where('period', 'seasonal')
            ->update(['period' => 'yearly']);

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['membership_season_start_month', 'membership_season_end_month']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->renameColumn('membership_allow_seasonal', 'membership_allow_yearly');
            $table->renameColumn('membership_fee_amount_seasonal', 'membership_fee_amount_yearly');
        });
    }
};
