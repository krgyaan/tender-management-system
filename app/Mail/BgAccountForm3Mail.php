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

class BgAccountForm3Mail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bank Guarantee submitted' . $this->data['tender_no'] . ' against ' . $this->data['purpose'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-account-form3-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->data['files'] ?? [] as $file) {
            $filePath = public_path("uploads/courier_docs/$file");

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

        foreach ($this->data['attachments'] ?? [] as $file) {
            $filePath = public_path("uploads/accounts/$file");

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
        return $attachments;
    }
}
