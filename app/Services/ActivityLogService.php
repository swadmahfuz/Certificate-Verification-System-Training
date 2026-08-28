<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ActivityLogService
{
    public function record(
        string $event,
        string $subjectType,
        ?int $subjectId,
        string $description,
        array $properties = []
    ): void {
        $properties['source_app'] = config('cvs.app_key');

        $payload = [
            'event' => $event,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'causer_id' => Auth::id(),
            'causer_name' => Auth::user() ? Auth::user()->name : null,
            'description' => $description,
            'properties' => $properties ? json_encode($properties) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ];

        foreach ($this->targetTables($subjectType) as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->insert($payload);
            }
        }
    }

    private function targetTables(string $subjectType): array
    {
        if ($this->isSharedSubjectType($subjectType)) {
            return collect(array_keys(config('cvs.apps', [])))
                ->map(fn (string $appKey) => $appKey . '_activity_logs')
                ->all();
        }

        return [$this->currentLogTable()];
    }

    private function isSharedSubjectType(string $subjectType): bool
    {
        return in_array($subjectType, config('cvs.shared_activity_subject_types', []), true);
    }

    private function currentLogTable(): string
    {
        return config('cvs.app_key', 'training') . '_activity_logs';
    }
}
