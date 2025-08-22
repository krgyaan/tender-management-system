<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Attachment;
use Illuminate\Support\Facades\Log;

class BgAccountForm1Mail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Bank Guarantee required in ' . $this->data['bg_needs'] . ' Hrs',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-account-form1-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->data['files'] ?? [] as $file) {
            $filePath = public_path("uploads/emds/$file");

            if (file_exists($filePath)) {
                try {
                    $attachments[] = Attachment::fromPath($filePath);
                } catch (\Exception $e) {
                    Log::error("Failed to open file at path: $filePath - Error: {$e->getMessage()}");
                }
            } else {
                Log::warning("File does not exist: $filePath");
            }
        }

        if (empty($this->data['pdfs']) || !is_array($this->data['pdfs'])) {
            Log::info('No files to attach.');
            return $attachments;
        }

        foreach ($this->data['pdfs'] as $file) {
            Log::info('Attaching file: ' . $file);
            $filePath = public_path('uploads/accounts/' . basename($file));

            if (file_exists($filePath)) {
                try {
                    $attachments[] = Attachment::fromPath($filePath);
                    Log::info('Attached file: ' . json_encode($attachments));
                } catch (\Exception $e) {
                    Log::error("Failed to open file at path: " . $filePath . " - Error: " . $e->getMessage());
                }
            } else {
                Log::warning('File does not exist: ' . $filePath);
            }
        }

        return $attachments;
    }
}
