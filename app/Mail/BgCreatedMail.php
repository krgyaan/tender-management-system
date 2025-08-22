<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BgCreatedMail extends Mailable
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
            subject: 'BG Created For ' . $this->data['purpose'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-created-mail',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        $files = array_filter($this->data['files'] ?? [], function ($file) {
            return !empty($file) && is_string($file);
        });
        
        foreach ($files as $file) {
            Log::debug('Files received:', $this->data['files'] ?? []);
            $filePath = public_path("uploads/emds/$file");
        
            Log::debug('Is readable? ' . (is_readable($filePath) ? 'yes' : 'no'));
            Log::debug('Is file? ' . (is_file($filePath) ? 'yes' : 'no'));

            Log::debug("Checking file path: $filePath");
        
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

        $pdfs = array_filter($this->data['pdfs'] ?? [], function ($file) {
            return !empty($file) && is_string($file);
        });

        foreach ($pdfs as $file) {
            Log::info('Attaching file: ' . $file);
            $filePath = public_path('uploads/bgpdfs/' . basename($file));

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
