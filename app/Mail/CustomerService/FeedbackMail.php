<?php

namespace App\Mail\CustomerService;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use \Illuminate\Mail\Mailables\Attachment;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public function __construct($data)
    {
        $this->data = $data;
          \Log::info('Data passed to FeedbackMail:', $data);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket No. ' . ($this->data['ticket_no'] ?? 'N/A') . ' - Customer Feedback',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.customer_service.feedback-mail',
            with: [
                'data' => $this->data,
            ],
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
            */
    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->data['attachment'])) {
            $filePath = storage_path('app/public/complaints/' . $this->data['attachment']);
            
            if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath)
                    ->as($this->data['attachment']) // optional: set filename for email
                    ->withMime(mime_content_type($filePath)); // optional: set MIME type
            }
        }

        return $attachments;
    }
}