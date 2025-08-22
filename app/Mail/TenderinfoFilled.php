<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenderinfoFilled extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tender Info ' . $this->data['tender_name'],
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'mail.tenderinfo-filled',
            with: ['data' => $this->data]
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
