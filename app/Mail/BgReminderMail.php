<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Log;

class BgReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $data)
    {
        $this->data = $data;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Expiring soon, Bank Guarantee No. ' . $this->data['bg_no'] . ' for ' . $this->data['project_name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.bg-reminder-mail',
            with: ['data' => $this->data],
        );
    }

    public function attachments(): array
    {
        $attachments = [];
        $files = [
            'soft_copy' => 'uploads/courier_docs/',
        ];

        foreach ($files as $key => $path) {
            if (isset($this->data[$key]) && $this->data[$key]) {
                $file = $this->data[$key];
                Log::info('Attaching file: ' . $file);
                $filePath = public_path($path . basename($file));

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
