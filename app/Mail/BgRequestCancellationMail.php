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

class BgRequestCancellationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cancellation for BG No. ' . $this->data['bg_no'] . ' and FDR No.' . $this->data['fdr_no'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-request-cancellation-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $files = [
            'stamp_covering_letter' => 'uploads/accounts/',
            'fdr_copy' => 'uploads/emds/',
            'bg_soft_copy' => 'uploads/courier_docs/',
            'soft_copy' => 'uploads/courier_docs/',
            'request_cancellation_pdf' => 'uploads/reqcancel/',
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
