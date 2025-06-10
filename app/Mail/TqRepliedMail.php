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
        try {
            if (isset($this->data['tq_document']) && !empty($this->data['tq_img'])) {
                $attachments[] = Attachment::fromPath('uploads/tq/' . $this->data['tq_document']);
            }
            if (isset($this->data['proof_submission']) && !empty($this->data['proof_submission'])) {
                $attachments[] = Attachment::fromPath('uploads/tq/' . $this->data['proof_submission']);
            }
            return $attachments;
        } catch (\Throwable $th) {
            Log::error("TQ Replied Attachment : " . $th->getMessage());
            return [];
        }
    }
}
