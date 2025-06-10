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

class PhydocsCreated extends Mailable
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
            subject: ' Physical Documents Courier ' . $this->data['tender_no'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.phydocs-created',
            with: ['data' => $this->data]
        );
    }
    public function attachments(): array
    {
        $attachmentArray = [];

        if (!empty($this->data['docketslip'])) {
            $docketSlips = explode(',', $this->data['docketslip']);
            foreach ($docketSlips as $attachment) {
                $attachment = trim($attachment);
                if (is_string($attachment) && !empty($attachment)) {
                    $fullPath = public_path('uploads/courier_docs/' . $attachment);
                    Log::info("Testing attachment: " . $attachment);

                    if (file_exists($fullPath)) {
                        $attachmentArray[] = Attachment::fromPath($fullPath);
                    } else {
                        Log::error("File not found at path: " . $fullPath);
                    }
                } else {
                    Log::error("Attachment is not a valid string: " . json_encode($attachment));
                }
            }
        } else {
            Log::error("Docket slip data is empty: " . json_encode($this->data['docketslip']));
        }

        return $attachmentArray;
    }
}
