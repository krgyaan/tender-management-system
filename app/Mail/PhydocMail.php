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

class PhydocMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    protected array $client;
    protected array $files;
    public function __construct(array $client, array $files)
    {
        $this->client = $client;
        $this->files = $files;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Phydoc Created For You',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.phydoc-mail',
            with: ['client' => $this->client]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachmentArray = [];

        if (!empty($this->files['files']) && is_array($this->files['files'])) {
            foreach ($this->files['files'] as $attachment) {
                if (is_string($attachment)) {
                    $fullPath = public_path('uploads/docs/' . $attachment);
                    Log::info("Testing attachment: " . $attachment);

                    if (file_exists(public_path('uploads/docs/' . $attachment))) {
                        $attachmentArray[] = Attachment::fromPath($fullPath);
                    } else {
                        Log::error("File not found at path: " . $fullPath);
                    }
                } else {
                    Log::error("Attachment is not a string as expected: " . json_encode($attachment));
                }
            }
        } else {
            Log::error("Attachments array is empty or not an array: " . json_encode($this->attachments));
        }

        return $attachmentArray;
    }
}
