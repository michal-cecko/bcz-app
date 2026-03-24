<?php

namespace App\Services;

use App\Enums\BillingPeriodEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\SubscriptionStatusEnum;
use App\Models\Payment;
use App\Models\SubscriptionPlan;
use App\Models\Team;
use App\Models\TeamSubscription;

class SubscriptionService
{
    public function __construct(
        private GoPayService $goPayService,
    ) {}

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

    /**
     * Create a subscription with initial GoPay recurring payment.
     * Returns the gateway URL for the first payment.
     */
    public function createSubscription(
        Team $team,
        SubscriptionPlan $plan,
        BillingPeriodEnum $billingPeriod,
        string $currency = 'EUR',
    ): array {
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

        $subscription = TeamSubscription::create([
            'team_id' => $team->id,
            'subscription_plan_id' => $plan->id,
            'status' => SubscriptionStatusEnum::ACTIVE,
            'billing_period' => $billingPeriod,
            'amount' => $price ?? 0,
            'currency' => $currency,
            'starts_at' => now(),
            'ends_at' => $endsAt,
        ]);

        // Free plans don't need GoPay
        if (! $price || $price <= 0) {
            return ['subscription' => $subscription, 'url' => null];
        }

        $orderNumber = 'SUB-'.$team->id.'-'.$subscription->id;
        $description = $plan->getTranslation('name', 'sk').' ('.$billingPeriod->value.')';

        $response = $this->goPayService->createRecurringPayment([
            'amount' => (int) round($price * 100),
            'currency' => $currency,
            'order_number' => substr($orderNumber, 0, 128),
            'description' => substr($description, 0, 256),
            'payer_email' => $team->members()->first()?->email ?? '',
            'items' => [[
                'name' => substr($description, 0, 256),
                'amount' => (int) round($price * 100),
            ]],
            'additional_params' => [
                ['name' => 'team_id', 'value' => (string) $team->id],
                ['name' => 'subscription_id', 'value' => (string) $subscription->id],
            ],
        ]);

        if ($response->hasSucceed()) {
            $subscription->update([
                'gopay_parent_payment_id' => (string) $response->json['id'],
            ]);

            Payment::create([
                'team_id' => $team->id,
                'user_id' => $team->members()->first()?->id,
                'payer_name' => $team->members()->first()?->name,
                'payer_email' => $team->members()->first()?->email,
                'payable_type' => 'team_subscription',
                'payable_id' => $subscription->id,
                'amount' => $price,
                'currency' => $currency,
                'status' => PaymentStatusEnum::PENDING,
                'payment_method' => PaymentMethodEnum::GOPAY,
                'gopay_payment_id' => (string) $response->json['id'],
            ]);

            return [
                'subscription' => $subscription,
                'url' => $response->json['gw_url'],
            ];
        }

        throw new \RuntimeException(
            'GoPay recurring payment creation failed: '.json_encode($response->json),
        );
    }

    public function cancelSubscription(TeamSubscription $subscription): TeamSubscription
    {
        // Void the recurrence on GoPay if we have a parent payment
        if ($subscription->gopay_parent_payment_id) {
            $this->goPayService->voidRecurrence((int) $subscription->gopay_parent_payment_id);
        }

        $subscription->update([
            'status' => SubscriptionStatusEnum::CANCELLED,
            'cancelled_at' => now(),
        ]);

        return $subscription;
    }

    /**
     * Charge the next recurring period for a subscription.
     */
    public function chargeRecurring(TeamSubscription $subscription): ?Payment
    {
        if (! $subscription->gopay_parent_payment_id) {
            return null;
        }

        $plan = $subscription->plan;
        $description = $plan->getTranslation('name', 'sk').' ('.$subscription->billing_period->value.')';
        $orderNumber = 'SUB-REC-'.$subscription->team_id.'-'.$subscription->id.'-'.now()->format('Ymd');

        $response = $this->goPayService->createRecurrence(
            (int) $subscription->gopay_parent_payment_id,
            [
                'amount' => (int) round($subscription->amount * 100),
                'currency' => $subscription->currency,
                'order_number' => substr($orderNumber, 0, 128),
                'description' => substr($description, 0, 256),
                'items' => [[
                    'name' => substr($description, 0, 256),
                    'amount' => (int) round($subscription->amount * 100),
                ]],
            ],
        );

        if ($response->hasSucceed()) {
            $newEndsAt = $subscription->billing_period === BillingPeriodEnum::YEARLY
                ? $subscription->ends_at->addYear()
                : $subscription->ends_at->addMonth();

            $subscription->update(['ends_at' => $newEndsAt]);

            $user = $subscription->team->members()->first();

            return Payment::create([
                'team_id' => $subscription->team_id,
                'user_id' => $user?->id,
                'payer_name' => $user?->name,
                'payer_email' => $user?->email,
                'payable_type' => 'team_subscription',
                'payable_id' => $subscription->id,
                'amount' => $subscription->amount,
                'currency' => $subscription->currency,
                'status' => PaymentStatusEnum::PENDING,
                'payment_method' => PaymentMethodEnum::GOPAY,
                'gopay_payment_id' => (string) $response->json['id'],
                'paid_at' => now(),
            ]);
        }

        return null;
    }

    public function changePlan(TeamSubscription $subscription, SubscriptionPlan $newPlan): array
    {
        return $this->createSubscription(
            $subscription->team,
            $newPlan,
            $subscription->billing_period,
            $subscription->currency,
        );
    }
}
