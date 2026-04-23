<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    /** @return list<string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        $resetUrl = route('filament.admin.auth.password-reset.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $subject = __('emails.reset_password.subject');

        return (new MailMessage)
            ->subject($subject)
            ->view('emails.reset-password', [
                'user' => $notifiable,
                'resetUrl' => $resetUrl,
                'expireMinutes' => $expireMinutes,
                'emailSubject' => $subject,
                'teamLogoUrl' => null,
                'teamUrl' => null,
                'teamEmail' => null,
                'teamPhone' => null,
                'teamWebsite' => null,
            ]);
    }
}
