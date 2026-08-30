<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BulkPdfService
{
    public function __construct(private CertificatePdfService $certificatePdfService)
    {
    }

    /**
     * @param  array<int>  $ids
     * @return array{path: string, generated_ids: array<int>, skipped: int}|null
     */
    public function generateZip(array $ids): ?array
    {
        $certificates = Certificate::whereIn('id', $ids)
            ->where('status', 'Approved')
            ->whereNotNull('certificate_type')
            ->where('certificate_type', '<>', '')
            ->whereNotNull('trainer_id')
            ->get()
            ->sortBy(fn ($certificate) => array_search($certificate->id, $ids))
            ->values();

        if ($certificates->isEmpty() || !class_exists(ZipArchive::class)) {
            return null;
        }

        $temporaryPath = tempnam(storage_path('app'), 'certificate-pdfs-');
        $zip = new ZipArchive();
        $zipIsOpen = false;

        try {
            if ($zip->open($temporaryPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Unable to create the certificate ZIP archive.');
            }

            $zipIsOpen = true;

            foreach ($certificates as $certificate) {
                $pdfContent = $this->certificatePdfService->generateTestPdf($certificate);
                $safeCertificateNumber = preg_replace('/[^A-Za-z0-9\-_.]/', '-', $certificate->certificate_number);
                $filename = 'Training-Certificate-' . $safeCertificateNumber . '-' . $certificate->id . '.pdf';

                if (!$zip->addFromString($filename, $pdfContent)) {
                    throw new \RuntimeException(
                        'Unable to add certificate ' . $certificate->certificate_number . ' to the ZIP archive.'
                    );
                }
            }

            $zip->close();
            $zipIsOpen = false;
        } catch (\Throwable $exception) {
            if ($zipIsOpen) {
                $zip->close();
            }

            if (is_file($temporaryPath)) {
                @unlink($temporaryPath);
            }

            Log::error('Bulk certificate PDF generation failed.', [
                'certificate_ids' => $ids,
                'exception' => $exception,
            ]);

            return null;
        }

        $generatedIds = $certificates->pluck('id')->all();
        $storagePath = 'bulk-downloads/' . uniqid('bulk_', true) . '.zip';
        Storage::disk('local')->put($storagePath, file_get_contents($temporaryPath));
        @unlink($temporaryPath);

        return [
            'path' => $storagePath,
            'generated_ids' => $generatedIds,
            'skipped' => count($ids) - count($generatedIds),
        ];
    }
}
