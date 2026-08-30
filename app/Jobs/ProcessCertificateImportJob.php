<?php

namespace App\Jobs;

use App\Imports\CertificateImport;
use App\Services\ActivityLogService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class ProcessCertificateImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private string $storedPath,
        private string $originalName,
        private int $userId,
        private string $userName
    ) {
    }

    public function handle(ActivityLogService $activityLog): void
    {
        $fullPath = Storage::disk('local')->path($this->storedPath);

        DB::transaction(function () use ($fullPath) {
            Excel::import(new CertificateImport, $fullPath);
        });

        $activityLog->record(
            'import.completed',
            'import',
            null,
            'Certificate data was imported from "' . $this->originalName . '" by ' . $this->userName . '.',
            ['file_name' => $this->originalName, 'queued' => true]
        );

        Storage::disk('local')->delete($this->storedPath);
    }

    public function failed(Throwable $exception): void
    {
        app(ActivityLogService::class)->record(
            'import.failed',
            'import',
            null,
            'Queued import of "' . $this->originalName . '" failed.',
            ['file_name' => $this->originalName, 'error' => $exception->getMessage()]
        );

        Storage::disk('local')->delete($this->storedPath);
    }
}
