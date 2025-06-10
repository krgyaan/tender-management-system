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

class FollowupStop extends Mailable
{
    use Queueable, SerializesModels;

    private $followup;
    public function __construct($followup)
    {
        $this->followup = $followup;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Follow Up Stopped',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.followup-stop',
            with: ['followup' => $this->followup]
        );
    }

    public function attachments(): array
    {
        $attachmentArray = [];

        if (!empty($this->followup['files']) && is_array($this->followup['files'])) {
            foreach ($this->followup['files'] as $attachment) {
                if (is_string($attachment)) {
                    $fullPath = public_path('uploads/accounts/' . $attachment);
                    // $fullPath = $attachment;
                    Log::info("Testing attachment: " . $attachment);

                    if (file_exists(public_path('uploads/accounts/' . $attachment))) {
                        $attachmentArray[] = Attachment::fromPath($fullPath);
                    } else {
                        Log::error("File not found at path: " . $fullPath);
                    }
                } else {
                    Log::error("Attachment is not a string as expected: " . json_encode($attachment));
                }
            }
        } else {
            Log::error("Attachments array is empty or not an array: " . json_encode($this->attachments));
        }

        return $attachmentArray;
    }
}
