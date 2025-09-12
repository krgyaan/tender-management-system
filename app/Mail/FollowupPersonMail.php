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

use function Pest\Laravel\json;

class FollowupPersonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Follow Up for ' . $this->data['for'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.followup-person-mail',
            with: ['data' => $this->data],
        );
    }
    public function attachments(): array
    {
        return [];
    }
}
