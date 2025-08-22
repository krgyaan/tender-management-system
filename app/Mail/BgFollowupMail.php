<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BgFollowupMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(private $data){}

    public function envelope(): Envelope
    {
        $status = $this->data['status'] ?? '';
        $tenderNo = $this->data['tender_no'] ?? '';
        $purpose = $this->data['purpose'] ?? '';
    
        $subject = "Bank Guarantee No. - {$status} - " . ($tenderNo ?: '') . " against {$purpose}";
    
        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-followup-mail',
            with: ['data' => $this->data]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
