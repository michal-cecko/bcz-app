<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InquiryReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $emailSubject;

    public ?string $teamName = null;

    public ?string $teamLogoUrl = null;

    public ?string $teamUrl = null;

    public ?string $teamEmail = null;

    public ?string $teamPhone = null;

    public ?string $teamWebsite = null;

    public function __construct(
        public Inquiry $inquiry,
    ) {
        $this->emailSubject = 'Nová správa z kontaktného formulára — '.$this->inquiry->name;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: [config('mail.from.address')],
            subject: $this->emailSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inquiry-received',
        );
    }
}
