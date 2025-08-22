<?php

namespace App\Mail;

use Illuminate\Mail\Attachment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ChequeAcForm extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cheque created - ' . $this->data['purpose'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.cheque-ac-form',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        if (empty($this->data['files']) || !is_array($this->data['files'])) {
            Log::info('No files to attach.');
            return $attachments;
        }

        foreach ($this->data['files'] as $file) {
            Log::info('Attaching file: ' . $file);
            $filePath = public_path('uploads/accounts/' . $file);

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
        
        foreach ($this->data['handover'] as $file) {
            Log::info('Attaching file: ' . $file);
            $filePath = public_path('uploads/accounts/' . $file);

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
