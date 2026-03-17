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
            $table->boolean('membership_allow_monthly')->default(false)->after('membership_enabled');
            $table->boolean('membership_allow_yearly')->default(false)->after('membership_allow_monthly');
            $table->decimal('membership_fee_amount_monthly', 10, 2)->nullable()->after('membership_allow_yearly');
            $table->decimal('membership_fee_amount_yearly', 10, 2)->nullable()->after('membership_fee_amount_monthly');
        });

        DB::table('teams')->whereNotNull('membership_period')->orderBy('id')->each(function (object $team) {
            $update = [];

            if ($team->membership_period === 'monthly') {
                $update['membership_allow_monthly'] = true;
                $update['membership_fee_amount_monthly'] = $team->membership_fee_amount;
            } elseif ($team->membership_period === 'yearly') {
                $update['membership_allow_yearly'] = true;
                $update['membership_fee_amount_yearly'] = $team->membership_fee_amount;
            }

            if ($update) {
                DB::table('teams')->where('id', $team->id)->update($update);
            }
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn(['membership_period', 'membership_fee_amount']);
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('membership_period')->nullable();
            $table->decimal('membership_fee_amount', 10, 2)->nullable();
        });

        DB::table('teams')
            ->where('membership_allow_monthly', true)
            ->update([
                'membership_period' => 'monthly',
                'membership_fee_amount' => DB::raw('membership_fee_amount_monthly'),
            ]);

        DB::table('teams')
            ->where('membership_allow_yearly', true)
            ->where('membership_allow_monthly', false)
            ->update([
                'membership_period' => 'yearly',
                'membership_fee_amount' => DB::raw('membership_fee_amount_yearly'),
            ]);

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn([
                'membership_allow_monthly',
                'membership_allow_yearly',
                'membership_fee_amount_monthly',
                'membership_fee_amount_yearly',
            ]);
        });
    }
};
