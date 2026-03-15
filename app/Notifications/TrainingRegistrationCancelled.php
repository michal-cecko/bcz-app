<?php

namespace App\Notifications;

use App\Models\Training;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrainingRegistrationCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Training $training,
        public ?string $reason = null,
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

        $mail = (new MailMessage)
            ->subject('Zrušenie registrácie na tréning')
            ->greeting('Dobrý deň,')
            ->line("Vaša registrácia na tréning **{$title}** bola zrušená.");

        if ($this->reason) {
            $mail->line("Dôvod: {$this->reason}");
        }

        return $mail->salutation('S pozdravom, tím BCZ');
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return [
            'training_id' => $this->training->id,
            'training_title' => $this->training->getTranslation('title', 'sk'),
            'reason' => $this->reason,
            'type' => 'training_registration_cancelled',
        ];
    }
}
