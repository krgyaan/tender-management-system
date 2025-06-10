<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TqMissedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TQ Missed - ' . $this->data['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.tq-missed-mail',
            with: [
                'data' => $this->data
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
