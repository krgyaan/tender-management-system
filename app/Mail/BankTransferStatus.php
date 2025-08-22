<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BankTransferStatus extends Mailable
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
            subject: 'Payment via Bank Transfer - ' . $this->data['purpose'],
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'mail.bank-transfer-status',
            with: ['data' => $this->data]
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
