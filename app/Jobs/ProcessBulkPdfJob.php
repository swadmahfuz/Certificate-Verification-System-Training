<?php

namespace App\Jobs;

use App\Services\ActivityLogService;
use App\Services\BulkPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessBulkPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @param array<int> $certificateIds */
    public function __construct(
        private array $certificateIds,
        private int $userId,
        private string $userName
    ) {
    }

    public function handle(BulkPdfService $bulkPdfService, ActivityLogService $activityLog): void
    {
        $result = $bulkPdfService->generateZip($this->certificateIds);

        if ($result === null) {
            throw new \RuntimeException('Bulk PDF generation produced no eligible certificates.');
        }

        $activityLog->record(
            'certificate.selected_bulk_pdf_generated',
            'certificate',
            null,
            count($result['generated_ids']) . ' selected certificate PDF(s) were generated (queued) for ' . $this->userName . '.',
            [
                'selected_ids' => $this->certificateIds,
                'generated_ids' => $result['generated_ids'],
                'generated_count' => count($result['generated_ids']),
                'skipped_count' => $result['skipped'],
                'download_path' => $result['path'],
                'queued' => true,
            ]
        );
    }

    public function failed(Throwable $exception): void
    {
        app(ActivityLogService::class)->record(
            'certificate.selected_bulk_pdf_failed',
            'certificate',
            null,
            'Queued bulk PDF generation failed for ' . $this->userName . '.',
            [
                'selected_ids' => $this->certificateIds,
                'error' => $exception->getMessage(),
            ]
        );
    }
}
