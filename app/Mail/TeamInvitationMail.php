<?php

namespace App\Mail;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class TeamInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $acceptUrl;

    public function __construct(public TeamInvitation $invitation)
    {
        $existingUser = User::where('email', $invitation->email)->exists();

        $routeName = $existingUser
            ? 'team-invitations.accept'
            : 'team-invitations.register';

        $this->acceptUrl = URL::temporarySignedRoute(
            $routeName,
            $invitation->expires_at,
            ['invitation' => $invitation->id],
        );
    }

    public function envelope(): Envelope
    {
        $teamName = $this->invitation->team->getTranslation('name', 'sk');

        return new Envelope(
            subject: "Pozvánka do tímu {$teamName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.team-invitation',
        );
    }
}
