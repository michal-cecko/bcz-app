<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Team;
use App\Models\TeamSubscription;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        Stripe::setApiKey(config('stripe.secret'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('stripe.webhook_secret'),
            );
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'account.updated' => $this->handleAccountUpdated($event->data->object),
            'invoice.paid' => $this->handleInvoicePaid($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            default => response()->json(['status' => 'ignored']),
        };
    }

    private function handleCheckoutCompleted(object $session): JsonResponse
    {
        $paymentService = app(PaymentService::class);
        $paymentService->handleCheckoutCompleted($session->id);

        return response()->json(['status' => 'ok']);
    }

    private function handleAccountUpdated(object $account): JsonResponse
    {
        $team = Team::where('stripe_connect_account_id', $account->id)->first();

        if ($team && ! $account->details_submitted) {
            $team->update(['stripe_connect_account_id' => null]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleInvoicePaid(object $invoice): JsonResponse
    {
        $subscriptionId = $invoice->subscription ?? null;

        if (! $subscriptionId) {
            return response()->json(['status' => 'ignored']);
        }

        $subscription = TeamSubscription::where('stripe_subscription_id', $subscriptionId)->first();

        if ($subscription) {
            $user = $subscription->team->members()->first();

            Payment::create([
                'team_id' => $subscription->team_id,
                'user_id' => $user?->id,
                'payer_name' => $user?->name,
                'payer_email' => $user?->email,
                'payable_type' => 'team_subscription',
                'payable_id' => $subscription->id,
                'amount' => $invoice->amount_paid / 100,
                'currency' => strtoupper($invoice->currency),
                'status' => 'completed',
                'payment_method' => 'stripe',
                'stripe_payment_id' => $invoice->payment_intent,
                'paid_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleSubscriptionUpdated(object $subscription): JsonResponse
    {
        $teamSub = TeamSubscription::where('stripe_subscription_id', $subscription->id)->first();

        if ($teamSub) {
            $statusMap = [
                'active' => 'active',
                'trialing' => 'trialing',
                'past_due' => 'past_due',
                'canceled' => 'cancelled',
                'unpaid' => 'past_due',
            ];

            $teamSub->update([
                'status' => $statusMap[$subscription->status] ?? $subscription->status,
                'ends_at' => $subscription->current_period_end
                    ? \Carbon\Carbon::createFromTimestamp($subscription->current_period_end)
                    : $teamSub->ends_at,
            ]);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleSubscriptionDeleted(object $subscription): JsonResponse
    {
        $teamSub = TeamSubscription::where('stripe_subscription_id', $subscription->id)->first();

        if ($teamSub) {
            $teamSub->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
