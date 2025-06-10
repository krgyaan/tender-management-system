<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChequeStopMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Arrange funds for Cheque - ' . $this->data['for'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.cheque-stop-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
