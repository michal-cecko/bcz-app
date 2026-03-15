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
        $locale = $notifiable->locale ?? 'sk';
        $title = $this->training->getTranslation('title', $locale)
            ?: $this->training->getTranslation('title', 'sk');

        $url = url($this->training->getLinkUrl());

        return (new MailMessage)
            ->subject('Uvoľnilo sa miesto na tréning')
            ->greeting('Dobrý deň,')
            ->line("Uvoľnilo sa miesto na tréning **{$title}**.")
            ->line('Zaregistrujte sa čo najskôr, miesta sú obmedzené.')
            ->action('Zobraziť tréning', $url)
            ->salutation('S pozdravom, tím BCZ');
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
