<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;

class PdfGeneratorService
{
    public function generatePdfs(string $formType, array $formData): array
    {
        Log::info("Generating PDFs for $formType", $formData);
        $method = 'generate' . ucfirst($formType) . 'Pdfs';
        if (method_exists($this, $method)) {
            return $this->$method($formData);
        }
        throw new \InvalidArgumentException("Form type '{$formType}' not supported");
    }

    protected function generateBgPdfs(array $formData): array
    {
        Log::info('Generating BG PDFs', $formData);

        $pdfFiles = [];

        // Generate BG Form PDFs
        $pdfTemplates = [
            'handover' => 'mail.pdfs.bg-handover',
            'indicative' => 'mail.pdfs.bg-indicative',
            'indicative2' => 'mail.pdfs.bg-indicative2',
            'undertaking' => 'mail.pdfs.bg-undertaking',
            'sfms' => 'mail.pdfs.bg-sfms',
            'yes-authorisation' => 'mail.pdfs.yes-authorisation',
            'set-off' => 'mail.pdfs.bg-set-off',
        ];

        foreach ($pdfTemplates as $name => $template) {
            Log::info("Generating PDF for $name");

            try {
                $pdf = PDF::loadview($template, ['data' => $formData])
                    ->setPaper('a4')
                    ->setOptions([
                        'defaultFont' => 'sans-serif',
                        'fontSize' => 12,
                        'isHtml5ParserEnabled' => true,
                        'isPhpEnabled' => true,
                        'isRemoteEnabled' => true,
                        'isFontSubsettingEnabled' => true,
                        'defaultMediaType' => 'screen',
                    ]);
                $filename = 'bg_' . $formData['id'] . '_' . $name . '_' . time() . '.pdf';
                $path = public_path('uploads/bgpdfs/' . $filename);
                file_put_contents($path, $pdf->output());
                $pdfFiles[] = $filename;
                Log::info("PDF successfully generated and saved: $path");
            } catch (Exception $e) {
                Log::error("Error generating PDF for $name: " . $e->getMessage());
            }
        }
        return $pdfFiles;
    }

    protected function generateChqCretPdfs(array $formData): array
    {
        $pdfFiles = [];

        try {
            $pdfTemplates = [
                'receiving' => 'mail.pdfs.chq-receiving',
            ];

            foreach ($pdfTemplates as $name => $template) {
                Log::info("Generating PDF for $name");

                $pdf = PDF::loadview($template, ['data' => $formData])
                    ->setPaper('a4')
                    ->setOptions([
                        'defaultFont' => 'sans-serif',
                        'fontSize' => 12,
                        'isHtml5ParserEnabled' => true,
                        'isPhpEnabled' => true,
                        'isRemoteEnabled' => true,
                        'isFontSubsettingEnabled' => true,
                        'defaultMediaType' => 'screen',
                    ]);

                $filename = 'chqcreate_' . $name . '_' . time() . '.pdf';
                // $path = 'chqcreate/' . $filename;
                $path = public_path('uploads/chqcreate/' . $filename);

                // Generate PDF with memory limit handling
                $output = null;
                $currentMemoryLimit = ini_get('memory_limit');
                ini_set('memory_limit', '512M');

                try {
                    $output = $pdf->output();
                } finally {
                    ini_set('memory_limit', $currentMemoryLimit);
                }

                if ($output) {
                    file_put_contents($path, $pdf->output());
                    $pdfFiles[] = $filename;
                    Log::info("PDF successfully generated and saved: $path");
                }
            }

            return $pdfFiles;
        } catch (Exception $e) {
            Log::error("PDF Generation Error: " . $e->getMessage());
            throw new Exception("Failed to generate PDF: " . $e->getMessage());
        }
    }

    protected function generateDdCancellationPdfs(array $formData): array
    {
        $pdfFiles = [];
        $pdfTemplates = [
            'dd_cancellation' => 'mail.pdfs.dd-cancellation'
        ];

        foreach ($pdfTemplates as $name => $template) {
            Log::info("Generating PDF for $name");

            $pdf = PDF::loadview($template, ['data' => $formData])
                ->setPaper('a4')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'fontSize' => 12,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'defaultMediaType' => 'screen',
                ]);

            $filename = 'ddcancel_' . $name . '_' . time() . '.pdf';
            $path = public_path('uploads/ddcancel/' . $filename);
            file_put_contents($path, $pdf->output());
            $pdfFiles[] = $filename;

            Log::info("PDF successfully generated and saved: $path");
        }
        return $pdfFiles;
    }

    protected function generateReqExtLetterPdfs(array $formData): array
    {
        $pdfFiles = [];
        $pdfTemplates = [
            'req_ext_letter' => 'mail.pdfs.req-ext-letter'
        ];

        foreach ($pdfTemplates as $name => $template) {
            Log::info("Generating PDF for $name");

            $pdf = PDF::loadview($template, ['data' => $formData])
                ->setPaper('a4')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'fontSize' => 12,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'defaultMediaType' => 'screen',
                ]);

            $filename = 'reqext_' . $name . '_' . time() . '.pdf';
            $path = public_path('uploads/reqext/' . $filename);
            file_put_contents($path, $pdf->output());
            $pdfFiles[] = $filename;

            Log::info("PDF successfully generated and saved: $path");
        }
        return $pdfFiles;
    }

    protected function generateReqCancelLetterPdfs(array $formData): array
    {
        $pdfFiles = [];
        $pdfTemplates = [
            'req_cancel_letter' => 'mail.pdfs.req-cancel-letter'
        ];

        foreach ($pdfTemplates as $name => $template) {
            Log::info("Generating PDF for $name");

            $pdf = PDF::loadview($template, ['data' => $formData])
                ->setPaper('a4')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'fontSize' => 12,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'defaultMediaType' => 'screen',
                ]);

            $filename = 'reqcancel_' . $name . '_' . time() . '.pdf';
            $path = public_path('uploads/reqcancel/' . $filename);
            file_put_contents($path, $pdf->output());
            $pdfFiles[] = $filename;

            Log::info("PDF successfully generated and saved: $path");
        }
        return $pdfFiles;
    }
    
    protected function generateDdFormatPdfs(array $formData): array
    {
        $pdfFiles = [];
        $pdfTemplates = [
            'dd_format' => 'mail.pdfs.dd-format'
        ];

        foreach ($pdfTemplates as $name => $template) {
            Log::info("Generating PDF for $name");

            $pdf = PDF::loadview($template, ['data' => $formData])
                ->setPaper([0, 0, 900, 500])
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'fontSize' => 12,
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true,
                    'isRemoteEnabled' => true,
                    'isFontSubsettingEnabled' => true,
                    'defaultMediaType' => 'screen',
                ]);

            $filename = 'ddformat_' . $name . '_' . time() . '.pdf';
            $path = public_path('uploads/ddformat/' . $filename);
            file_put_contents($path, $pdf->output());
            $pdfFiles[] = $filename;

            Log::info("PDF successfully generated and saved: $path");
        }
        return $pdfFiles;
    }
}
