<?php

namespace App\Jobs;

use App\Services\PdfGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $formType;
    private array $formData;
    private int $emdId;

    public function __construct(string $formType, array $formData, int $emdId)
    {
        $this->formType = $formType;
        $this->formData = $formData;
        $this->emdId = $emdId;
        $this->onQueue('pdf-generation');
    }

    public function handle(PdfGeneratorService $pdfGenerator)
    {
        Log::info("Starting PDF generation job", [
            'form_type' => $this->formType,
            'emd_id' => $this->emdId
        ]);

        try {
            $pdfFiles = $pdfGenerator->generatePdfs($this->formType, $this->formData);
            return $pdfFiles;
        } catch (\Exception $e) {
            Log::error("PDF generation failed", [
                'error' => $e->getMessage(),
                'emd_id' => $this->emdId
            ]);
            throw $e;
        }
    }
}
