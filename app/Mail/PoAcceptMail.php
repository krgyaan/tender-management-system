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

class PoAcceptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'PO Accepted - ' . $this->data['number'] . ' dated ' . $this->data['date'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.po-accept-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $files = [
            'contract' => 'upload/applicable',
            'pbg' => 'upload/applicable',
            'signed_wo' => 'upload/acceptance',
        ];
        Log::info('For Attachments : ' . json_encode($files));
        foreach ($files as $fileKey => $path) {
            Log::info("Checking if file exists: $fileKey");
            if (isset($this->data[$fileKey]) && $this->data[$fileKey]) {
                Log::info("File exists: $fileKey");
                $filePath = public_path($path . '/' . basename($this->data[$fileKey]));
                Log::info("File path: $filePath");

                if (file_exists($filePath)) {
                    try {
                        $attachments[] = Attachment::fromPath($filePath);
                        Log::info('Attached file: ' . json_encode($attachments));
                    } catch (\Exception $e) {
                        Log::error("Failed to open file at path: $filePath - Error: {$e->getMessage()}");
                    }
                } else {
                    Log::warning("File does not exist: $filePath");
                }
            }
        }
        Log::info('Final attachments: ' . json_encode($attachments));
        return $attachments;
    }
}
