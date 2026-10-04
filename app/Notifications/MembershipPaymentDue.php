<?php

namespace App\Notifications;

use App\Models\Membership;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class MembershipPaymentDue extends Notification implements ShouldQueue
{
    use Queueable;

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
        $paymentService = app(PaymentService::class);
        $feeAmount = number_format($paymentService->amountStillOwed($this->membership), 2);
        $feeCurrency = $this->membership->fee_currency;
        $paymentDeadline = $this->membership->payment_deadline_at?->format('d.m.Y') ?? '';

        $payment = $paymentService->openPaymentFor($this->membership, $user, $this->membership->team);
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
}
