<?php

namespace App\Services;

use App\Enums\BillingPeriodEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamSubscription;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionService
{
    public function __construct()
    {
        if (config('stripe.secret')) {
            Stripe::setApiKey(config('stripe.secret'));
        }
    }

    public function assignFreePlan(Team $team): TeamSubscription
    {
        $freePlan = SubscriptionPlan::where('tier', 'free')->first();

        if (! $freePlan) {
            throw new \RuntimeException('Free plan not found. Please run SubscriptionPlanSeeder.');
        }

        return TeamSubscription::create([
            'team_id' => $team->id,
            'subscription_plan_id' => $freePlan->id,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'billing_period' => BillingPeriodEnum::MONTHLY,
            'amount' => 0,
            'currency' => 'EUR',
            'starts_at' => now(),
        ]);
    }

    public function createSubscription(
        Team $team,
        SubscriptionPlan $plan,
        BillingPeriodEnum $billingPeriod,
        string $currency = 'EUR',
    ): TeamSubscription {
        $price = $plan->getPriceForCurrency(
            $currency,
            $billingPeriod === BillingPeriodEnum::YEARLY ? 'yearly' : 'monthly',
        );

        $endsAt = $billingPeriod === BillingPeriodEnum::YEARLY
            ? now()->addYear()
            : now()->addMonth();

        // Cancel any existing active subscription
        $team->subscriptions()
            ->where('status', SubscriptionStatusEnum::ACTIVE)
            ->update([
                'status' => SubscriptionStatusEnum::CANCELLED,
                'cancelled_at' => now(),
            ]);

        return TeamSubscription::create([
            'team_id' => $team->id,
            'subscription_plan_id' => $plan->id,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'billing_period' => $billingPeriod,
            'amount' => $price ?? 0,
            'currency' => $currency,
            'starts_at' => now(),
            'ends_at' => $endsAt,
        ]);
    }

    public function cancelSubscription(TeamSubscription $subscription): TeamSubscription
    {
        if ($subscription->stripe_subscription_id) {
            Subscription::update($subscription->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);
        }

        $subscription->update([
            'status' => SubscriptionStatusEnum::CANCELLED,
            'cancelled_at' => now(),
        ]);

        return $subscription;
    }

    public function changePlan(TeamSubscription $subscription, SubscriptionPlan $newPlan): TeamSubscription
    {
        return $this->createSubscription(
            $subscription->team,
            $newPlan,
            $subscription->billing_period,
            $subscription->currency,
        );
    }
}
