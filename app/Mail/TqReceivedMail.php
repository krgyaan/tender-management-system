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

class TqReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TQ Received - ' . $this->data['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.tq-received-mail',
            with: [
                'data' => $this->data
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        try {
            if ($this->data['files']) {
                foreach ($this->data['files'] as $file) {
                    $attachments[] = Attachment::fromPath('uploads/tq/' .  $file);
                }
            }
            return $attachments;
        } catch (\Throwable $th) {
            Log::error("TQ Received Attachment: " . $th->getMessage());
            return [];
        }
    }
}
