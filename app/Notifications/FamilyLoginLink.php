<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class FamilyLoginLink extends Notification
{
    public function __construct(public string $returnPath) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject(__('family.login_subject'))->line(__('family.login_body'))
            ->action(__('family.login'), URL::temporarySignedRoute('magic-login', now()->addMinutes(30), [
                'user' => $notifiable->id, 'return' => $this->returnPath,
            ]));
    }
}
