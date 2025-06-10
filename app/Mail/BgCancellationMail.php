<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BgCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancellation for BG No. ' . $this->data['bg_no'] . ' and FDR No.' . $this->data['fdr_no'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-cancellation-mail',
            with: ['data' => $this->data],
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
