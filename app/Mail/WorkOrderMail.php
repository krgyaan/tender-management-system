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

class WorkOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Received Order From - ' . $this->data['organization_name'] . ' against ' . $this->data['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.work-order-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (empty($this->data['files'])) {

            foreach ($this->data['files'] ?? [] as $file) {
                $filePath = public_path("uploads/docs/$file");

                if (is_string($file) && file_exists($filePath)) {
                    $attachments[] = Attachment::fromPath($filePath);
                } else {
                    Log::error("Invalid attachment: $file");
                }
            }
        }
        return $attachments;
    }
}
