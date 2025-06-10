<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BtStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    private $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bank Transfer EMD Status Changed',
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'mail.bt-status-mail',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
