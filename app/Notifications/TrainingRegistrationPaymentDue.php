<?php

namespace App\Notifications;

use App\Models\TrainingRegistration;
use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TrainingRegistrationPaymentDue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TrainingRegistration $registration,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $training = $this->registration->training;
        $team = $training?->team;
        $teamName = $team?->getTranslation('name', 'sk') ?? '';
        $trainingTitle = $training?->getTranslation('title', 'sk') ?? 'Tréning';
        $paymentService = app(PaymentService::class);
        $feeAmount = number_format($paymentService->amountStillOwed($this->registration), 2);
        $feeCurrency = $this->registration->getPriceCurrency();
        $paymentDeadline = $this->registration->payment_due_at?->format('d.m.Y') ?? '';

        $payment = $paymentService->openPaymentFor($this->registration, $user, $team);
        $paymentUrl = $payment
            ? URL::signedRoute('payment.page', ['payment' => $payment->id])
            : url('/admin');

        return (new MailMessage)
            ->subject('Platba za tréning — '.$trainingTitle)
            ->view('emails.training-payment-due', [
                'user' => $user,
                'registration' => $this->registration,
                'training' => $training,
                'teamName' => $teamName,
                'trainingTitle' => $trainingTitle,
                'feeAmount' => $feeAmount,
                'feeCurrency' => $feeCurrency,
                'paymentDeadline' => $paymentDeadline,
                'paymentUrl' => $paymentUrl,
                'emailSubject' => 'Platba za tréning',
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
            'training_registration_id' => $this->registration->id,
            'training_title' => $this->registration->training?->getTranslation('title', 'sk'),
            'fee_amount' => $this->registration->training?->price_amount,
            'payment_due_at' => $this->registration->payment_due_at?->toIso8601String(),
            'type' => 'training_registration_payment_due',
        ];
    }
}
