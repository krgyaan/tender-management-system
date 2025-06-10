<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenderStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Update Status of ' . $this->data['emdType'] . ' - ' . $this->data['purpose'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.tender-status-update-mail',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
