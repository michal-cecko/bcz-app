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
use App\Models\TeamSeason;
use App\Models\Training;
use App\Models\TrainingRegistration;
use App\Models\User;
use App\Notifications\EventRegistrationPaymentDue;
use App\Notifications\MembershipPaymentDue;
use App\Notifications\PaymentConfirmed;
use App\Notifications\TrainingPaymentConfirmed;
use App\Notifications\TrainingRegistrationPaymentDue;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

class PaymentService
{
    public function __construct(
        private GoPayService $goPayService,
    ) {}

    /**
     * Record a payment an admin took by hand. A completed one is run through
     * {@see self::processPaymentCompleted()}, so the payable is only approved or
     * activated once its completed payments cover the full price.
     */
    public function recordManualPayment(
        ?User $user,
        Team|string $team,
        Model $payable,
        float $amount,
        string $currency,
        PaymentMethodEnum|string $paymentMethod,
        ?string $notes = null,
        bool $notify = true,
        PaymentStatusEnum $status = PaymentStatusEnum::COMPLETED,
        ?CarbonInterface $paidAt = null,
    ): Payment {
        $paymentMethod = $paymentMethod instanceof PaymentMethodEnum ? $paymentMethod : PaymentMethodEnum::from($paymentMethod);

        $payment = Payment::create([
            'team_id' => $team instanceof Team ? $team->id : $team,
            'user_id' => $user?->id,
            'payer_name' => $user?->name,
            'payer_email' => $user?->email,
            'payable_type' => $payable->getMorphClass(),
            'payable_id' => $payable->getKey(),
            'amount' => $amount,
            'currency' => $currency,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
            'paid_at' => $status === PaymentStatusEnum::COMPLETED ? ($paidAt ?? now()) : $paidAt,
        ]);

        if ($paymentMethod === PaymentMethodEnum::BANK_TRANSFER) {
            $payment->refresh();
            $payment->update(['variable_symbol' => $this->variableSymbolFor($payment)]);
        }

        if ($status === PaymentStatusEnum::COMPLETED) {
            $payment->setRelation('payable', $payable->fresh() ?? $payable);
            $this->processPaymentCompleted($payment, $notify);
        }

        return $payment;
    }

    /**
     * Email the payer the payment request again, with the amount still owed and
     * the open payment's link. Returns false when there is nothing to ask for:
     * no payer, the item is no longer awaiting payment, or nothing is owed.
     */
    public function resendPaymentRequest(Model $payable): bool
    {
        $payable->loadMissing('user');
        $user = $payable->user;

        $notification = match (true) {
            $payable instanceof Membership => $payable->status === MembershipStatusEnum::PENDING
                && ! $payable->is_free
                && $user?->isMembershipPayer($payable->team)
                    ? new MembershipPaymentDue($payable)
                    : null,
            $payable instanceof TrainingRegistration => $payable->status === RegistrationStatusEnum::Pending
                && $payable->training?->pricing_type === TrainingPricingTypeEnum::PAID
                    ? new TrainingRegistrationPaymentDue($payable)
                    : null,
            $payable instanceof EventRegistration => $payable->status === RegistrationStatusEnum::Pending
                    ? new EventRegistrationPaymentDue($payable)
                    : null,
            default => null,
        };

        if (! $notification || ! $user || $this->amountStillOwed($payable) <= 0) {
            return false;
        }

        $user->notify($notification);

        return true;
    }

    public function amountStillOwed(Payable $payable): float
    {
        if (! $payable instanceof Model) {
            return 0.0;
        }

        $totalPaid = (float) $payable->payments()
            ->where('status', PaymentStatusEnum::COMPLETED)
            ->where('currency', $payable->getPriceCurrency())
            ->sum('amount');

        return max(0.0, round($payable->getTotalPriceAmount() - $totalPaid, 2));
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

        if ($payable instanceof Payable) {
            $this->settleOpenPayments($payable, $payment);
        }

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

    /**
     * After a completed payment, keep the payable's other open payments in line:
     * once it is paid in full they are cancelled, so nobody pays twice; while
     * something is still owed, the newest one asks for exactly that and older
     * duplicates are cancelled. GoPay payments are left alone, the gateway may
     * still take them.
     */
    protected function settleOpenPayments(Payable&Model $payable, Payment $completed): void
    {
        $owed = $this->amountStillOwed($payable);

        $openPayments = $payable->payments()
            ->where('status', PaymentStatusEnum::PENDING)
            ->whereNull('gopay_payment_id')
            ->whereKeyNot($completed->getKey())
            ->latest('created_at')
            ->get();

        $keep = $owed > 0
            ? $openPayments->first(fn (Payment $payment): bool => $payment->currency === $payable->getPriceCurrency())
            : null;

        foreach ($openPayments as $openPayment) {
            if ($keep?->is($openPayment)) {
                $openPayment->update(['amount' => $owed]);
            } else {
                $openPayment->update(['status' => PaymentStatusEnum::CANCELLED]);
            }
        }
    }

    /**
     * Put a payable back to waiting for payment when a payment that counted
     * towards it was refunded, changed or deleted and the rest no longer covers
     * the full price.
     */
    public function reopenIfNoLongerPaid(?Model $payable): void
    {
        if (! $payable instanceof Payable || $payable->getTotalPriceAmount() <= 0 || $this->isFullyPaid($payable)) {
            return;
        }

        // The payment's cached payable can predate later edits; the resets
        // below only write attributes that differ from what is loaded.
        $payable->refresh();

        // A new due date, same length as when first issued, and a fresh reminder,
        // so the reminder and the expiry sweeps treat it like a new one.
        if ($payable instanceof Membership && $payable->status === MembershipStatusEnum::ACTIVE) {
            $payable->update([
                'status' => MembershipStatusEnum::PENDING,
                'payment_deadline_at' => now()->addDays($payable->season?->payment_deadline_days ?? 14),
                'payment_reminder_sent_at' => null,
            ]);
        }

        if ($payable instanceof TrainingRegistration && $payable->status === RegistrationStatusEnum::Approved) {
            $payable->update(['status' => RegistrationStatusEnum::Pending, 'payment_due_at' => now()->addDays(7), 'payment_reminder_sent_at' => null]);
        }

        if ($payable instanceof EventRegistration && $payable->status === RegistrationStatusEnum::Approved) {
            $payable->update(['status' => RegistrationStatusEnum::Pending, 'payment_due_at' => now()->addDays(14), 'payment_reminder_sent_at' => null]);
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
     *
     * For a payable, the payment asks only for what is still owed after its
     * completed (partial) payments, and an older one is brought up to date.
     */
    public function ensurePendingPaymentFor(
        User $user,
        Team $team,
        Model $payable,
        float $amount,
        string $currency = 'EUR',
    ): Payment {
        if ($payable instanceof Payable) {
            $amount = $this->amountStillOwed($payable);
        }

        // Any open payment on the payable counts, also one recorded for a guest.
        $existing = Payment::query()
            ->where('payable_type', $payable->getMorphClass())
            ->where('payable_id', $payable->getKey())
            ->where('status', PaymentStatusEnum::PENDING)
            ->latest('created_at')
            ->first();

        if ($existing) {
            if ($payable instanceof Payable && ! $existing->gopay_payment_id && $amount > 0
                && $existing->currency === $currency && (float) $existing->amount !== $amount) {
                $existing->update(['amount' => $amount]);
            }

            return $existing;
        }

        return $this->createPendingPayment($user, $team, $payable, $amount, $currency);
    }

    /**
     * The open payment the payer should use for what is still owed on the
     * payable, created if missing. Null when nothing is owed.
     */
    public function openPaymentFor(Payable&Model $payable, User $user, ?Team $team): ?Payment
    {
        if (! $team || $this->amountStillOwed($payable) <= 0) {
            return null;
        }

        return $this->ensurePendingPaymentFor(
            user: $user,
            team: $team,
            payable: $payable,
            amount: $this->amountStillOwed($payable),
            currency: $payable->getPriceCurrency(),
        );
    }

    /**
     * Return the pending membership fee payment for the user on this team's season,
     * creating the Membership and its Payment when they do not exist yet. Returns
     * null when the user owes nothing: outside membership billing, a free
     * membership, or one that is already active.
     *
     * The payment always charges the membership's own fee, which decides when the
     * membership counts as paid; see {@see SeasonService::findOrCreateMembership()}.
     */
    public function ensurePendingMembershipPayment(User $user, Team $team, TeamSeason $season): ?Payment
    {
        $membership = app(SeasonService::class)->findOrCreateMembership($season, $user);

        if (! $membership || $membership->is_free || $membership->status === MembershipStatusEnum::ACTIVE
            || $this->amountStillOwed($membership) <= 0) {
            return null;
        }

        return $this->ensurePendingPaymentFor(
            user: $user,
            team: $team,
            payable: $membership,
            amount: $membership->getTotalPriceAmount(),
            currency: $membership->getPriceCurrency(),
        );
    }

    /**
     * Return the membership fee the user still owes because they signed up for a
     * training that requires club membership, or null when they owe none.
     *
     * Scoped to the teams behind those registrations, so an unrelated pending
     * membership on another team never leaks into the welcome email.
     */
    public function pendingMembershipPaymentFromMembershipRequiredTraining(User $user): ?Payment
    {
        $teamIds = Training::query()
            ->where('pricing_type', TrainingPricingTypeEnum::MEMBERSHIP_REQUIRED)
            ->whereHas('registrations', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('status', '!=', RegistrationStatusEnum::Cancelled->value))
            ->pluck('team_id');

        if ($teamIds->isEmpty()) {
            return null;
        }

        return Payment::query()
            ->where('user_id', $user->id)
            ->where('payable_type', (new Membership)->getMorphClass())
            ->where('status', PaymentStatusEnum::PENDING)
            ->whereIn('team_id', $teamIds)
            ->latest('created_at')
            ->first();
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
