<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Attachment;


class DdChqAcceptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $data)
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
            view: 'mail.dd-chq-accept-mail',
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

        if (empty($this->data['pdf']) || !is_array($this->data['pdf'])) {
            Log::info('No files DD Format to attach.');
            return $attachments;
        }

        foreach ($this->data['pdf'] as $file) {
            Log::info('Attaching file: ' . $file);
            $filePath = public_path('uploads/ddformat/' . basename($file));

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
