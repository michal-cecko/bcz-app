<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('stripe_payment_id', 'gopay_payment_id');
            $table->renameColumn('stripe_checkout_session_id', 'gopay_order_number');
            $table->dropColumn('stripe_transfer_id');
        });

        Schema::table('team_subscriptions', function (Blueprint $table) {
            $table->renameColumn('stripe_subscription_id', 'gopay_parent_payment_id');
        });

        Schema::table('subscription_plan_prices', function (Blueprint $table) {
            $table->dropColumn(['stripe_monthly_price_id', 'stripe_yearly_price_id']);
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->dropColumn('stripe_product_id');
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('stripe_connect_account_id');
        });

        // Update existing payment records from 'stripe' to 'gopay'
        DB::table('payments')
            ->where('payment_method', 'stripe')
            ->update(['payment_method' => 'gopay']);

        // Update payment_methods_enabled on teams
        $teams = DB::table('teams')->get();
        foreach ($teams as $team) {
            if ($team->payment_methods_enabled) {
                $methods = json_decode($team->payment_methods_enabled, true);
                if (is_array($methods)) {
                    $methods = array_map(fn ($m) => $m === 'stripe' ? 'gopay' : $m, $methods);
                    DB::table('teams')
                        ->where('id', $team->id)
                        ->update(['payment_methods_enabled' => json_encode($methods)]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('gopay_payment_id', 'stripe_payment_id');
            $table->renameColumn('gopay_order_number', 'stripe_checkout_session_id');
            $table->string('stripe_transfer_id')->nullable()->after('stripe_checkout_session_id');
        });

        Schema::table('team_subscriptions', function (Blueprint $table) {
            $table->renameColumn('gopay_parent_payment_id', 'stripe_subscription_id');
        });

        Schema::table('subscription_plan_prices', function (Blueprint $table) {
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_yearly_price_id')->nullable();
        });

        Schema::table('subscription_plans', function (Blueprint $table) {
            $table->string('stripe_product_id')->nullable();
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->string('stripe_connect_account_id')->nullable();
        });

        DB::table('payments')
            ->where('payment_method', 'gopay')
            ->update(['payment_method' => 'stripe']);
    }
};
