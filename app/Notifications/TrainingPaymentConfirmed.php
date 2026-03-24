<?php

namespace App\Notifications;

use App\Models\Payment;
use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingPaymentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Training $training,
        public ?Payment $payment = null,
    ) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $user = $notifiable;
        $trainingTitle = $this->training->getTranslation('title', $user->locale ?? 'sk')
            ?: $this->training->getTranslation('title', 'sk');

        $team = $this->training->team;

        return (new MailMessage)
            ->subject('Platba potvrdená — '.$trainingTitle)
            ->view('emails.training-payment-confirmed', [
                'user' => $user,
                'training' => $this->training,
                'payment' => $this->payment,
                'trainingTitle' => $trainingTitle,
                'emailSubject' => 'Platba potvrdená',
                'teamName' => $team?->getTranslation('name', 'sk'),
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
            'training_id' => $this->training->id,
            'training_title' => $this->training->getTranslation('title', 'sk'),
            'type' => 'training_payment_confirmed',
        ];
    }
}
