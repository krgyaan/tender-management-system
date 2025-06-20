<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RaResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'RA Result - ' . $this->data['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.ra-result-mail',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
