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

class BgRequestExtensionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: ' Extension/Modification of BG No.' . $this->data['bg_no'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-request-extension-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $files = [
            'ext_letter' => 'uploads/accounts/',
            'fdr_copy' => 'uploads/emds/',
            'bg_soft_copy' => 'uploads/emds/',
            'soft_copy' => 'uploads/courier_docs/',
            'request_extension_pdf' => 'uploads/reqext/',
        ];

        foreach ($files as $key => $path) {
            if (isset($this->data[$key]) && $this->data[$key]) {
                $file = $this->data[$key];
                Log::info('Attaching file: ' . $file);
                $filePath = public_path($path . basename($file));

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
        }

        return $attachments;
    }
}
