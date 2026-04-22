<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class WelcomeToApp extends Notification implements ShouldQueue
{
    use Queueable;

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        /** @var User $user */
        $user = $notifiable;

        $magicUrl = URL::temporarySignedRoute('magic-login', now()->addDays(7), ['user' => $user->id]);

        return (new MailMessage)
            ->subject('Vitaj v BCZ App!')
            ->view('emails.welcome', [
                'user' => $user,
                'magicUrl' => $magicUrl,
                'emailSubject' => 'Vitaj v BCZ App!',
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
            'type' => 'welcome_to_app',
        ];
    }
}
