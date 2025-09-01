<?php

namespace App\Mail\CustomerService;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use \Illuminate\Mail\Mailables\Attachment;

class CustomerMail extends Mailable
{
    use Queueable, SerializesModels;


    public $data;
    public function __construct($data)
    {
        $this->data = $data;
          \Log::info('Data passed to CustomerMail:', $data);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket Number - ' . ($this->data['ticket_no'] ?? 'N/A') . ' Service Request received',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.customer_service.customer-mail',
            with: [
                'data' => $this->data,
            ],
        );
    }
    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
            */
    public function attachments(): array
    {
        $attachments = [];

        return $attachments;
    }
}