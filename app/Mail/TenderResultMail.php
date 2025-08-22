<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;

class TenderResultMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Tender ' . $this->data['tender_name'] . ' - Result ' . $this->data['result'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.tender-result-mail',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        try {
            if ($this->data['files']) {
                $files = $this->data['files'];
                foreach ($files as $file) {
                    $filePath = "uploads/tender-results/$file";
                    Log::info("Attempting to attach file: $filePath");
                    if (file_exists($filePath)) {
                        $attachments[] = Attachment::fromPath($filePath);
                        Log::info("Successfully attached file: $filePath");
                    } else {
                        Log::warning("File does not exist: $filePath");
                    }
                }
            } else {
                Log::info("No files found to attach.");
            }
        } catch (\Throwable $th) {
            Log::error("Error in attaching files: " . $th->getMessage());
        }

        return $attachments;
    }
}
