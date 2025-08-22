<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BidSubmissionSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $bid)
    {
        $this->bid = $bid;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bid Submitted - ' . $this->bid['tenderName'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bid-submission-submitted',
            with: ['data' => $this->bid],
        );
    }

    public function attachments(): array
    {
        $attachments = [];

        if (!empty($this->bid['files']) && is_array($this->bid['files'])) {
            foreach ($this->bid['files'] as $file) {
                $filePath = public_path('bid_submissions/' . $file);
                if (file_exists($filePath)) {
                    try {
                        $attachments[] = Attachment::fromPath($filePath);
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
