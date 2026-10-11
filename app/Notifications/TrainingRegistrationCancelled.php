<?php

namespace App\Notifications;

use App\Models\Member;
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
        public ?Member $member = null,
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

        $trainingTitle = ($this->member?->name ? $this->member->name.' — ' : '').$trainingTitle;

        return (new MailMessage)
            ->subject('Zrušenie registrácie na tréning — '.$trainingTitle)
            ->view('emails.training-cancelled', [
                'user' => $user,
                'training' => $this->training,
                'trainingTitle' => $trainingTitle,
                'reason' => $this->reason,
                'emailSubject' => 'Zrušenie registrácie na tréning',
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
            'member_id' => $this->member?->id,
            'training_title' => $this->training->getTranslation('title', 'sk'),
            'reason' => $this->reason,
            'type' => 'training_registration_cancelled',
        ];
    }
}
