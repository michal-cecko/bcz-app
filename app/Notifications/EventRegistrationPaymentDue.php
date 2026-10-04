<?php

namespace App\Notifications;

use App\Models\EventRegistration;
use App\Services\PaymentService;
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
        $paymentService = app(PaymentService::class);
        $feeAmount = number_format($paymentService->amountStillOwed($this->registration), 2);
        $feeCurrency = $this->registration->getPriceCurrency();
        $paymentDeadline = $this->registration->payment_due_at?->format('d.m.Y') ?? '';

        $payment = $paymentService->openPaymentFor($this->registration, $user, $this->registration->event?->team);
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
}
