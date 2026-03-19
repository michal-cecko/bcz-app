<?php

namespace App\Services;

use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\TrainingPaymentConfirmed;
use Illuminate\Database\Eloquent\Model;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\Stripe;

class PaymentService
{
    public function __construct()
    {
        if (config('stripe.secret')) {
            Stripe::setApiKey(config('stripe.secret'));
        }
    }

    public function recordManualPayment(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency,
        PaymentMethodEnum $paymentMethod,
        ?string $notes = null,
    ): Payment {
        return Payment::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'payer_name' => $user->name,
            'payer_email' => $user->email,
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => PaymentStatusEnum::COMPLETED,
            'payment_method' => $paymentMethod,
            'variable_symbol' => $paymentMethod === PaymentMethodEnum::BANK_TRANSFER
                ? $this->generateVariableSymbol()
                : null,
            'notes' => $notes,
            'paid_at' => now(),
        ]);
    }

    public function createCheckoutSession(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency,
        string $successUrl,
        string $cancelUrl,
    ): array {
        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'unit_amount' => (int) ($amount * 100),
                    'product_data' => [
                        'name' => class_basename($payable).' #'.$payable->getKey(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => [
                'team_id' => $team->id,
                'user_id' => $user->id,
                'payable_type' => $payable->getMorphClass(),
                'payable_id' => $payable->getKey(),
            ],
        ];

        if ($team->stripe_connect_account_id) {
            $feePercent = (float) ($team->settings()->where('key', 'stripe_platform_fee_percent')->value('value') ?? 5);
            $applicationFee = (int) ($amount * 100 * $feePercent / 100);

            $sessionParams['payment_intent_data'] = [
                'transfer_data' => [
                    'destination' => $team->stripe_connect_account_id,
                ],
                'application_fee_amount' => $applicationFee,
            ];
        }

        $session = Session::create($sessionParams);

        $payment = Payment::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'payer_name' => $user->name,
            'payer_email' => $user->email,
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => PaymentStatusEnum::PENDING,
            'payment_method' => PaymentMethodEnum::STRIPE,
            'stripe_checkout_session_id' => $session->id,
        ]);

        return [
            'url' => $session->url,
            'payment' => $payment,
        ];
    }

    public function handleCheckoutCompleted(string $sessionId): ?Payment
    {
        $payment = Payment::where('stripe_checkout_session_id', $sessionId)->first();

        if (! $payment) {
            return null;
        }

        $session = Session::retrieve($sessionId);

        $payment->update([
            'status' => PaymentStatusEnum::COMPLETED,
            'stripe_payment_id' => $session->payment_intent,
            'paid_at' => now(),
        ]);

        if ($payment->payable instanceof Membership) {
            $payment->payable->update(['status' => MembershipStatusEnum::ACTIVE]);

            // Auto-approve pending registrations for MEMBERSHIP_REQUIRED trainings
            $this->autoApprovePendingRegistrationsForMembership($payment->payable);
        }

        if ($payment->payable instanceof TrainingRegistration) {
            $registration = $payment->payable;
            $registration->update([
                'status' => RegistrationStatusEnum::Approved,
                'payment_due_at' => null,
            ]);

            if ($registration->user) {
                $registration->user->notify(new TrainingPaymentConfirmed($registration->training));
            }
        }

        return $payment;
    }

    public function refund(Payment $payment, ?string $notes = null): Payment
    {
        if ($payment->payment_method === PaymentMethodEnum::STRIPE && $payment->stripe_payment_id) {
            Refund::create([
                'payment_intent' => $payment->stripe_payment_id,
            ]);
        }

        $payment->update([
            'status' => PaymentStatusEnum::REFUNDED,
            'refunded_at' => now(),
            'notes' => $notes ? ($payment->notes ? $payment->notes."\n".$notes : $notes) : $payment->notes,
        ]);

        return $payment;
    }

    /**
     * Auto-approve all pending training registrations for MEMBERSHIP_REQUIRED trainings
     * when a membership becomes active.
     */
    protected function autoApprovePendingRegistrationsForMembership(Membership $membership): void
    {
        $pendingRegistrations = TrainingRegistration::query()
            ->where('user_id', $membership->user_id)
            ->where('status', RegistrationStatusEnum::Pending)
            ->whereHas('training', function ($query) use ($membership) {
                $query->where('team_id', $membership->team_id)
                    ->where('pricing_type', TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED);
            })
            ->with('training')
            ->get();

        foreach ($pendingRegistrations as $registration) {
            $registration->update(['status' => RegistrationStatusEnum::Approved]);

            if ($registration->user) {
                $registration->user->notify(new TrainingPaymentConfirmed($registration->training));
            }
        }
    }

    public function generateVariableSymbol(): string
    {
        do {
            $symbol = (string) random_int(1000000000, 9999999999);
        } while (Payment::where('variable_symbol', $symbol)->exists());

        return $symbol;
    }
}
