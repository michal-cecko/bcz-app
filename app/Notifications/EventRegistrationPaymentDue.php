<?php

namespace App\Notifications;

use App\Enums\PaymentStatusEnum;
use App\Models\EventRegistration;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class EventRegistrationPaymentDue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EventRegistration $registration,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $event = $this->registration->event;
        $team = $event?->team;
        $teamName = $team?->getTranslation('name', 'sk') ?? '';
        $eventTitle = $event?->getTranslation('title', 'sk') ?? 'Podujatie';
        $feeAmount = number_format($this->registration->getTotalPriceAmount(), 2);
        $feeCurrency = $this->registration->getPriceCurrency();
        $paymentDeadline = $this->registration->payment_due_at?->format('d.m.Y') ?? '';

        $payment = $this->findOrCreatePendingPayment($user);
        $paymentUrl = $payment
            ? URL::signedRoute('payment.page', ['payment' => $payment->id])
            : url('/admin');

        return (new MailMessage)
            ->subject('Platba za podujatie — '.$eventTitle)
            ->view('emails.event-payment-due', [
                'user' => $user,
                'registration' => $this->registration,
                'event' => $event,
                'teamName' => $teamName,
                'eventTitle' => $eventTitle,
                'feeAmount' => $feeAmount,
                'feeCurrency' => $feeCurrency,
                'paymentDeadline' => $paymentDeadline,
                'paymentUrl' => $paymentUrl,
                'emailSubject' => 'Platba za podujatie',
                'teamLogoUrl' => $team?->getFirstMediaUrl('logo') ?: null,
                'teamUrl' => $team ? url('/timy/'.$team->slug) : null,
                'teamEmail' => $team?->contact_email,
                'teamPhone' => $team?->contact_phone,
                'teamWebsite' => $team?->contact_website,
            ]);
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'event_registration_id' => $this->registration->id,
            'event_title' => $this->registration->event?->getTranslation('title', 'sk'),
            'fee_amount' => $this->registration->getTotalPriceAmount(),
            'fee_currency' => $this->registration->getPriceCurrency(),
            'payment_due_at' => $this->registration->payment_due_at?->toIso8601String(),
            'type' => 'event_registration_payment_due',
        ];
    }

    private function findOrCreatePendingPayment(object $user): ?Payment
    {
        $registration = $this->registration;
        $event = $registration->event;
        $amount = $registration->getTotalPriceAmount();

        if (! $event?->team_id || $amount <= 0) {
            return null;
        }

        $existing = Payment::query()
            ->where('payable_type', $registration->getMorphClass())
            ->where('payable_id', $registration->id)
            ->whereIn('status', [PaymentStatusEnum::PENDING, PaymentStatusEnum::COMPLETED])
            ->first();

        if ($existing) {
            return $existing;
        }

        return Payment::create([
            'team_id' => $event->team_id,
            'user_id' => $user->id ?? null,
            'payer_name' => $user->name ?? null,
            'payer_email' => $user->email ?? null,
            'payable_type' => $registration->getMorphClass(),
            'payable_id' => $registration->getKey(),
            'amount' => $amount,
            'currency' => $registration->getPriceCurrency(),
            'status' => PaymentStatusEnum::PENDING,
        ]);
    }
}
