<?php

namespace App\Notifications;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingSpotAvailable extends Notification implements ShouldQueue
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
        $trainingUrl = url($this->training->getLinkUrl());

        return (new MailMessage)
            ->subject('Uvoľnilo sa miesto na tréning — '.$trainingTitle)
            ->view('emails.training-spot-available', [
                'user' => $user,
                'training' => $this->training,
                'trainingTitle' => $trainingTitle,
                'trainingUrl' => $trainingUrl,
                'emailSubject' => 'Uvoľnilo sa miesto na tréning',
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
            'training_url' => $this->training->getLinkUrl(),
            'type' => 'training_spot_available',
        ];
    }
}
