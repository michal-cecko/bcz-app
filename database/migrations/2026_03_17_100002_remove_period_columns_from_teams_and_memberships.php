<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn('period');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'membership_allow_monthly',
                'membership_allow_seasonal',
                'membership_fee_amount_monthly',
                'membership_fee_amount_seasonal',
                'membership_season_start_month',
                'membership_season_end_month',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->boolean('membership_allow_monthly')->default(false);
            $table->boolean('membership_allow_seasonal')->default(false);
            $table->decimal('membership_fee_amount_monthly', 10, 2)->nullable();
            $table->decimal('membership_fee_amount_seasonal', 10, 2)->nullable();
            $table->unsignedTinyInteger('membership_season_start_month')->nullable();
            $table->unsignedTinyInteger('membership_season_end_month')->nullable();
        });

        Schema::table('memberships', function (Blueprint $table) {
            $table->string('period')->nullable();
        });
    }
};
