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

class SignedContractAgreementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Signed Contract Agreement For WO - ' . $this->data['wo_no'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.signed-contract-agreement-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->data['files'] ?? [] as $file) {
            $filePath = public_path("uploads/docs/$file");

            if (is_string($file) && file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath);
            } else {
                Log::error("Invalid attachment: $file");
            }
        }

        return $attachments;
    }
}
