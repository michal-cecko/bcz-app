<?php

namespace App\Notifications;

use App\Enums\PaymentStatusEnum;
use App\Models\Membership;
use App\Models\Payment;
use App\Notifications\Concerns\GeneratesPaymentQrCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MembershipPaymentDue extends Notification implements ShouldQueue
{
    use GeneratesPaymentQrCode, Queueable;

    public function __construct(
        public Membership $membership,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $teamName = $this->membership->team?->getTranslation('name', 'sk') ?? '';
        $seasonName = $this->membership->season?->name ?? '';
        $feeAmount = number_format((float) $this->membership->fee_amount, 2);
        $feeCurrency = $this->membership->fee_currency;
        $paymentDeadline = $this->membership->payment_deadline_at?->format('d.m.Y') ?? '';

        $payment = $this->findOrCreatePendingPayment($user);
        $paymentUrl = $payment
            ? URL::signedRoute('payment.page', ['payment' => $payment->id])
            : url('/admin');

        return (new MailMessage)
            ->subject('Platba za členstvo — '.$seasonName)
            ->view('emails.membership-payment-due', [
                'user' => $user,
                'membership' => $this->membership,
                'teamName' => $teamName,
                'seasonName' => $seasonName,
                'feeAmount' => $feeAmount,
                'feeCurrency' => $feeCurrency,
                'paymentDeadline' => $paymentDeadline,
                'paymentUrl' => $paymentUrl,
                'qrCodeImage' => $this->qrCodeImageForPayment($payment),
                'emailSubject' => 'Platba za členstvo',
                'teamLogoUrl' => null,
                'teamUrl' => null,
                'teamEmail' => null,
                'teamPhone' => null,
                'teamWebsite' => null,
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'membership_id' => $this->membership->id,
            'team_name' => $this->membership->team?->getTranslation('name', 'sk'),
            'season_name' => $this->membership->season?->name,
            'fee_amount' => $this->membership->fee_amount,
            'fee_currency' => $this->membership->fee_currency,
            'payment_deadline_at' => $this->membership->payment_deadline_at?->toIso8601String(),
            'type' => 'membership_payment_due',
        ];
    }

    /**
     * Find an existing pending payment for this membership or create one.
     */
    private function findOrCreatePendingPayment(object $user): ?Payment
    {
        $membership = $this->membership;

        if (! $membership->team_id || (float) $membership->fee_amount <= 0) {
            return null;
        }

        $existing = Payment::query()
            ->where('payable_type', 'membership')
            ->where('payable_id', $membership->id)
            ->where('status', PaymentStatusEnum::PENDING)
            ->first();

        if ($existing) {
            return $existing;
        }

        return Payment::create([
            'team_id' => $membership->team_id,
            'user_id' => $user->id ?? null,
            'payer_name' => $user->name ?? null,
            'payer_email' => $user->email ?? null,
            'payable_type' => $membership->getMorphClass(),
            'payable_id' => $membership->getKey(),
            'amount' => $membership->fee_amount,
            'currency' => $membership->fee_currency,
            'status' => PaymentStatusEnum::PENDING,
        ]);
    }
}
