<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use function Pest\Laravel\from;

class TenderCreated extends Mailable
{
    use Queueable, SerializesModels;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Tender - ' . $this->data['tenderName'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.tender-created',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachmentArray = [];

        if (!empty($this->data['files']) && is_array($this->data['files'])) {
            foreach ($this->data['files'] as $attachment) {
                if (is_string($attachment)) {
                    $fullPath = public_path('uploads/docs/' . $attachment);
                    // $fullPath = $attachment;
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
