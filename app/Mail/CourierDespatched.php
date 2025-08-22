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

class CourierDespatched extends Mailable
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
            subject: 'Courier sent to ' . $this->data['to_org'],
        );
    }
    public function content(): Content
    {
        return new Content(
            view: 'mail.courier-despatched',
            with: $this->data,
        );
    }
    
    public function attachments(): array
    {
        try {
            foreach ($this->data['files'] ?? [] as $file) {
                $filePath = public_path("uploads/courier_docs/$file");
    
                if (file_exists($filePath)) {
                    try {
                        $attachments[] = Attachment::fromPath($filePath);
                    } catch (\Exception $e) {
                        Log::error("Failed to open file at path: $filePath - Error: {$e->getMessage()}");
                    }
                } else {
                    Log::warning("File does not exist: $filePath");
                }
            }
            return $attachments;
        } catch (\Throwable $th) {
            Log::error("CourierDespatched: " . $th->getMessage());
            return [];
        }
    }
}
