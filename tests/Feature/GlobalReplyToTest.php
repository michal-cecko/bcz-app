<?php

namespace Tests\Feature;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GlobalReplyToTest extends TestCase
{
    public function test_global_reply_to_is_applied_to_outgoing_mail(): void
    {
        config([
            'mail.default' => 'array',
            'mail.reply_to' => ['address' => 'reply@bcz-club.com', 'name' => 'BCZ Club'],
        ]);
        Mail::forgetMailers();

        Mail::to('user@example.com')->send(new class extends Mailable
        {
            public function build(): self
            {
                return $this->subject('Test')->html('<p>Hi</p>');
            }
        });

        $messages = Mail::mailer('array')->getSymfonyTransport()->messages();
        $this->assertCount(1, $messages);

        $replyTo = $messages[0]->getOriginalMessage()->getReplyTo();
        $this->assertCount(1, $replyTo);
        $this->assertSame('reply@bcz-club.com', $replyTo[0]->getAddress());
        $this->assertSame('BCZ Club', $replyTo[0]->getName());
    }
}
