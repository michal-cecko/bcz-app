<?php

namespace App\Services;

use App\Contracts\Payable;
use App\Enums\MembershipStatusEnum;
use App\Enums\PaymentMethodEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\RegistrationStatusEnum;
use App\Enums\TrainingPricingTypeEnum;
use App\Models\EventRegistration;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Team;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\PaymentConfirmed;
use App\Notifications\TrainingPaymentConfirmed;
use Illuminate\Database\Eloquent\Model;

class PaymentService
{
    public function __construct(
        private GoPayService $goPayService,
    ) {}

    public function recordManualPayment(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency,
        PaymentMethodEnum $paymentMethod,
        ?string $notes = null,
        bool $notify = true,
    ): Payment {
        $payment = Payment::create([
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
            'notes' => $notes,
            'paid_at' => now(),
        ]);

        if ($paymentMethod === PaymentMethodEnum::BANK_TRANSFER) {
            $payment->refresh();
            $payment->update(['variable_symbol' => $this->variableSymbolFor($payment)]);
        }

        $payment->setRelation('payable', $payable->fresh() ?? $payable);

        $this->processPaymentCompleted($payment, $notify);

        return $payment;
    }

    /**
     * Check whether the sum of completed payments for this payable covers its full price.
     */
    public function isFullyPaid(Payable $payable): bool
    {
        if (! $payable instanceof Model) {
            return false;
        }

        $totalPrice = $payable->getTotalPriceAmount();

        if ($totalPrice <= 0) {
            return true;
        }

        $totalPaid = (float) $payable->payments()
            ->where('status', PaymentStatusEnum::COMPLETED)
            ->where('currency', $payable->getPriceCurrency())
            ->sum('amount');

        return ($totalPaid + 0.005) >= $totalPrice;
    }

    /**
     * Create a GoPay payment and return the gateway URL for redirect.
     */
    public function createGoPayPayment(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency,
    ): array {
        $orderNumber = strtoupper(substr(class_basename($payable), 0, 3)).'-'.now()->format('ymd').'-'.random_int(1000, 9999);
        $description = $payable instanceof Payable
            ? $payable->getPaymentDescription()
            : class_basename($payable).' #'.$payable->getKey();

        $response = $this->goPayService->createPayment([
            'amount' => (int) round($amount * 100),
            'currency' => $currency,
            'order_number' => substr($orderNumber, 0, 128),
            'description' => substr($description, 0, 256),
            'payer_email' => $user->email,
            'items' => [[
                'name' => substr($description, 0, 256),
                'amount' => (int) round($amount * 100),
            ]],
            'additional_params' => [
                ['name' => 'team_id', 'value' => (string) $team->id],
                ['name' => 'payable_type', 'value' => $payable->getMorphClass()],
                ['name' => 'payable_id', 'value' => (string) $payable->getKey()],
            ],
        ]);

        if ($response->hasSucceed()) {
            $goPayId = $response->json['id'];

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
                'payment_method' => PaymentMethodEnum::GOPAY,
                'gopay_payment_id' => (string) $goPayId,
                'gopay_order_number' => $response->json['order_number'] ?? null,
            ]);

            return [
                'url' => $response->json['gw_url'],
                'payment' => $payment,
            ];
        }

        throw new \RuntimeException(
            'GoPay payment creation failed: '.json_encode($response->json),
        );
    }

    /**
     * Handle GoPay notification — verify payment status and process business logic.
     */
    public function handleGoPayNotification(int $goPayId): ?Payment
    {
        $payment = Payment::where('gopay_payment_id', (string) $goPayId)->first();

        if (! $payment) {
            return null;
        }

        $response = $this->goPayService->getPaymentStatus($goPayId);

        if (! $response->hasSucceed()) {
            return null;
        }

        $state = $response->json['state'] ?? null;

        if ($state === 'PAID' && $payment->status !== PaymentStatusEnum::COMPLETED) {
            $payment->update([
                'status' => PaymentStatusEnum::COMPLETED,
                'paid_at' => now(),
            ]);

            $this->processPaymentCompleted($payment);
        }

        if ($state === 'REFUNDED') {
            $payment->update([
                'status' => PaymentStatusEnum::REFUNDED,
                'refunded_at' => now(),
            ]);
        }

        return $payment;
    }

    /**
     * Process business logic after a payment is completed.
     *
     * Activates the underlying payable only when the sum of completed payments
     * covers its full price (so partial / installment payments are supported).
     */
    public function processPaymentCompleted(Payment $payment, bool $notify = true): void
    {
        $payable = $payment->payable;

        if (! $payable) {
            return;
        }

        $isFullyPaid = $payable instanceof Payable && $this->isFullyPaid($payable);

        if ($payable instanceof Membership) {
            if ($isFullyPaid && $payable->status !== MembershipStatusEnum::ACTIVE) {
                $payable->update(['status' => MembershipStatusEnum::ACTIVE]);
                $this->autoApprovePendingRegistrationsForMembership($payable, $notify);
            }

            if ($notify && $payment->user) {
                $payment->user->notify(new PaymentConfirmed($payment));
            }
        }

        if ($payable instanceof TrainingRegistration) {
            if ($isFullyPaid && $payable->status !== RegistrationStatusEnum::Approved) {
                $payable->update([
                    'status' => RegistrationStatusEnum::Approved,
                    'payment_due_at' => null,
                ]);
            }

            if ($notify && $payable->user) {
                $payable->user->notify(new PaymentConfirmed($payment));
            }
        }

        if ($payable instanceof EventRegistration) {
            if ($isFullyPaid && $payable->status !== RegistrationStatusEnum::Approved) {
                $payable->update(['status' => RegistrationStatusEnum::Approved]);
            }

            if ($notify && $payment->user) {
                $payment->user->notify(new PaymentConfirmed($payment));
            }
        }
    }

    public function refund(Payment $payment, ?string $notes = null): Payment
    {
        if ($payment->payment_method === PaymentMethodEnum::GOPAY && $payment->gopay_payment_id) {
            $amountInCents = (int) round($payment->amount * 100);
            $this->goPayService->refundPayment((int) $payment->gopay_payment_id, $amountInCents);
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
    protected function autoApprovePendingRegistrationsForMembership(Membership $membership, bool $notify = true): void
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

            if ($notify && $registration->user) {
                $registration->user->notify(new TrainingPaymentConfirmed($registration->training));
            }
        }
    }

    /**
     * Create a pending payment record for a registration that requires payment.
     */
    public function createPendingPayment(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency = 'EUR',
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
            'status' => PaymentStatusEnum::PENDING,
        ]);
    }

    /**
     * Return the latest pending Payment for this user+payable, creating one if missing.
     * Used by widgets that need a stable VS to display before the user picks a method.
     */
    public function ensurePendingPaymentFor(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency = 'EUR',
    ): Payment {
        $existing = Payment::query()
            ->where('user_id', $user->id)
            ->where('payable_type', $payable->getMorphClass())
            ->where('payable_id', $payable->getKey())
            ->where('status', PaymentStatusEnum::PENDING)
            ->latest('created_at')
            ->first();

        if ($existing) {
            return $existing;
        }

        return $this->createPendingPayment($user, $team, $payable, $amount, $currency);
    }

    /**
     * Build the 8-digit zero-padded variable symbol from the payment's sequence_number.
     * The payment must already be persisted so the sequence_number is assigned.
     */
    public function variableSymbolFor(Payment $payment): string
    {
        if (empty($payment->sequence_number)) {
            $payment->refresh();
        }

        if (empty($payment->sequence_number)) {
            throw new \LogicException('Payment must be persisted before a variable symbol can be generated.');
        }

        return str_pad((string) $payment->sequence_number, 8, '0', STR_PAD_LEFT);
    }
}
