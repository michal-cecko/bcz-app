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

        if (in_array($state, ['CANCELED', 'TIMEOUTED'])) {
            $payment->update([
                'status' => PaymentStatusEnum::FAILED,
            ]);
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
     */
    private function processPaymentCompleted(Payment $payment): void
    {
        if ($payment->payable instanceof Membership) {
            $payment->payable->update(['status' => MembershipStatusEnum::ACTIVE]);
            $this->autoApprovePendingRegistrationsForMembership($payment->payable);

            if ($payment->user) {
                $payment->user->notify(new PaymentConfirmed($payment));
            }
        }

        if ($payment->payable instanceof TrainingRegistration) {
            $registration = $payment->payable;
            $registration->update([
                'status' => RegistrationStatusEnum::Approved,
                'payment_due_at' => null,
            ]);

            if ($registration->user) {
                $registration->user->notify(new PaymentConfirmed($payment));
            }
        }

        if ($payment->payable instanceof EventRegistration) {
            $payment->payable->update(['status' => RegistrationStatusEnum::Approved]);

            if ($payment->user) {
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

    public function generateVariableSymbol(): string
    {
        do {
            $symbol = (string) random_int(1000000000, 9999999999);
        } while (Payment::where('variable_symbol', $symbol)->exists());

        return $symbol;
    }
}
