<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BankTransfer extends Mailable
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
            subject: 'New EMD - Bank Transfer Request',
        );
    }
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.bank-transfer',
            with: ['data' => $this->data],
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
