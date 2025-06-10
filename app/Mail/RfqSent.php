<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RfqSent extends Mailable
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
            subject: 'RFQ ' . $this->data['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.rfq-sent',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        try {
            if ($this->data['files']) {
                foreach ($this->data['files'] as $key => $file) {
                    $attachments[] = Attachment::fromPath('uploads/rfqdocs/' . $file);
                }
            }
            return $attachments;
        } catch (\Throwable $th) {
            Log::error('Error in attachments: ' . $th->getMessage());
            return [];
        }
    }
}
