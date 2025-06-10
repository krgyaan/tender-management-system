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

class MinuteOfMeetingMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Minute Of Meeting For WO - ' . $this->data['wo_no'] . ' on ' . $this->data['date'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.minute-of-meeting-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        foreach ($this->data['files'] ?? [] as $file) {
            $filePath = public_path("upload/applicable/$file");

            if (is_string($file) && file_exists($filePath)) {
                $attachments[] = Attachment::fromPath($filePath);
            } else {
                Log::error("Invalid attachment: $file");
            }
        }

        return $attachments;
    }
}
