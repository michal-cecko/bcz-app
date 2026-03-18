<?php

namespace App\Notifications;

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

        return (new MailMessage)
            ->subject('Platba potvrdená — '.$trainingTitle)
            ->view('emails.training-payment-confirmed', [
                'user' => $user,
                'training' => $this->training,
                'trainingTitle' => $trainingTitle,
                'emailSubject' => 'Platba potvrdená',
                'teamName' => null,
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
            'training_id' => $this->training->id,
            'training_title' => $this->training->getTranslation('title', 'sk'),
            'type' => 'training_payment_confirmed',
        ];
    }
}
