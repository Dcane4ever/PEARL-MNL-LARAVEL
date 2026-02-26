<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class MagicLoginLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $magicLink,
        public Carbon $expiresAt,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Secure Login Link - The Pearl Manila',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.magic-login-link',
            with: [
                'user' => $this->user,
                'magicLink' => $this->magicLink,
                'expiresAt' => $this->expiresAt,
            ],
        );
    }
}
