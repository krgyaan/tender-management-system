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
        $attachments = [];
        try {
            Log::info('Attempting to attach files...');
            if (!empty($this->data['files']) && is_array($this->data['files'])) {
                foreach ($this->data['files'] as $file) {
                    Log::info('Attaching file: ' . $file);
                    $filePath = public_path('uploads/accounts/' . $file);
                    if (file_exists($filePath)) {
                        Log::info('File exists: ' . $filePath);
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
            } else {
                Log::info('No files to attach.');
            }
            return $attachments;
        } catch (\Throwable $th) {
            Log::error('Error in attachments: ' . $th->getMessage());
            return [];
        }
    }
}
