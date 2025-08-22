<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Attachment;

class DdAccountFormMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New DD - ' . $this->data['purpose'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.dd-account-form-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $files = json_decode($this->data['files'], true);
        if($files) {
            foreach ($files as $file) {
                Log::info('Trying to attach file: ' . $file);
                $filePath = public_path('uploads/courier_docs/' . $file);
    
                Log::info('File path: ' . $filePath);
                Log::info('File exists: ' . (file_exists($filePath) ? 'yes' : 'no'));
                if (file_exists($filePath)) {
                    try {
                        $attachments[] = Attachment::fromPath($filePath);
                        Log::info('Attached file: ' . json_encode($attachments));
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
