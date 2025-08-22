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

class TqRepliedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'TQ Replied - ' . $this->data['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.tq-replied-mail',
            with: [
                'data' => $this->data
            ]
        );
    }

    public function attachments(): array
    {
        $attachments = [];
    
        try {
            if (!empty($this->data['tq_document'])) {
                $path = 'uploads/tq/' . $this->data['tq_document'];
                if (file_exists($path)) {
                    $attachments[] = Attachment::fromPath($path);
                }
            }
    
            if (!empty($this->data['proof_submission'])) {
                $path = 'uploads/tq/' . $this->data['proof_submission'];
                if (file_exists($path)) {
                    $attachments[] = Attachment::fromPath($path);
                }
            }
        } catch (\Throwable $th) {
            Log::error("TQ Replied Attachment : " . $th->getMessage());
        }
    
        return $attachments;
    }
}
