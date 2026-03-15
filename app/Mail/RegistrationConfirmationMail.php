<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class RegistrationConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $magicUrl;

    public function __construct(
        public User $user,
        public string $registrationType,
        public string $registrationTitle,
        public bool $isNewUser = false,
    ) {
        $this->magicUrl = URL::temporarySignedRoute(
            'magic-login',
            now()->addDays(7),
            ['user' => $user->id],
        );
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Potvrdenie registrácie — {$this->registrationTitle}",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.registration-confirmation',
        );
    }
}
