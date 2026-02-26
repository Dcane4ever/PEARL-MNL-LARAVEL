<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public string $subjectLine,
        public string $messageLine,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-status',
            with: [
                'booking' => $this->booking,
                'messageLine' => $this->messageLine,
            ],
        );
    }
}
