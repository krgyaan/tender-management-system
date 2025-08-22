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

class CourierStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Courier has been ' . $this->data['status'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.courier-status-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->data['pod']) && is_array($this->data['pod'])) {
            foreach ($this->data['pod'] as $file) {
                if (is_string($file)) {
                    $filePath = public_path('uploads/courier_docs/' . $file);
                    Log::info("Processing attachment: " . $file);

                    if (file_exists($filePath)) {
                        $attachments[] = Attachment::fromPath($filePath);
                    } else {
                        Log::error("File not found at path: " . $filePath);
                    }
                } else {
                    Log::error("Invalid attachment format: " . json_encode($file));
                }
            }
        } else {
            Log::error("Attachments data is invalid: " . json_encode($this->data['pod']));
        }

        return $attachments;
    }
}
