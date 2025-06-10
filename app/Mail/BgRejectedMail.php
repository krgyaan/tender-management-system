<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BgRejectedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bank Guarantee rejected ' . $this->data['purpose'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-rejected-mail',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
