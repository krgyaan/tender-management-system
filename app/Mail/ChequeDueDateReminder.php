<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChequeDueDateReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $cheque)
    {
        $this->cheque = $cheque;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Arrange funds for Cheque - ' . $this->cheque['for'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.cheque-due-date-reminder',
            with: ['data' => $this->cheque],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
