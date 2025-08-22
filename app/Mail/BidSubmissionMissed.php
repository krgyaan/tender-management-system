<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BidSubmissionMissed extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $bid)
    {
        $this->bid = $bid;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bid Missed - ' . $this->bid['tender_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bid-submission-missed',
            with: ['data' => $this->bid],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
