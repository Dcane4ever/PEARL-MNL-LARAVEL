<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class LoginActivityCheckMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $yesUrl,
        public string $noUrl,
        public string $ipAddress,
        public string $userAgent,
        public Carbon $loggedInAt,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login Activity Verification - The Pearl Manila',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.login-activity-check',
            with: [
                'user' => $this->user,
                'yesUrl' => $this->yesUrl,
                'noUrl' => $this->noUrl,
                'ipAddress' => $this->ipAddress,
                'userAgent' => $this->userAgent,
                'loggedInAt' => $this->loggedInAt,
            ],
        );
    }
}
