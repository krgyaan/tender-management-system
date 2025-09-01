<?php

namespace App\Mail\CustomerService;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use \Illuminate\Mail\Mailables\Attachment;

class ConferenceCallMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public function __construct($data)
    {
        $this->data = $data;
          \Log::info('Data passed to Conference Call:', $data);
    }

    /**
     * Get the message envelope.
     */
public function envelope(): Envelope
{
    return new Envelope(
        subject: 'Ticket No. - ' . ($this->data['ticket_no'] ?? 'N/A') . ', details of the issue',
    );
}

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.customer_service.conference-call-mail',
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

        // Handle photos/doc attachments
        if (!empty($this->data['attachments'])) {
            foreach ($this->data['attachments'] as $file) {
                $filePath = storage_path('app/public/' . $file);
                if (file_exists($filePath)) {
                    $attachments[] = Attachment::fromPath($filePath)
                        ->as(basename($filePath))
                        ->withMime(mime_content_type($filePath));
                }
            }
        }

        // Handle voice recording
        if (!empty($this->data['voice_recording'])) {
            $filePath = storage_path('app/public/' . $this->data['voice_recording']);
            if (file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath)
                    ->as(basename($filePath))
                    ->withMime(mime_content_type($filePath));
            }
        }

        return $attachments;
    }
}