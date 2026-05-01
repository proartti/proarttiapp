<?php

namespace App\Mail;

use App\Models\LoginToken;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $magicUrl;

    public function __construct(LoginToken $token)
    {
        $this->magicUrl = route('magic-link.verify', ['token' => $token->token]);
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your sign-in link');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.magic-link');
    }
}
