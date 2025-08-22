<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Mailables\Attachment;

class CourierCreated extends Mailable
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
            subject: 'New Courier to ' . $this->data['to_org'],
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'mail.courier-created',
            with: $this->data,
        );
    }
    public function attachments(): array
    {
        try {
            if ($this->data['files']) {
                foreach ($this->data['files'] as $file) {
                    $attachments[] = Attachment::fromPath('uploads/courier_docs/' .  $file);
                }
            }
            return $attachments;
        } catch (\Throwable $th) {
            Log::error("CourierCreated: " . $th->getMessage());
            return [];
        }
    }
}
