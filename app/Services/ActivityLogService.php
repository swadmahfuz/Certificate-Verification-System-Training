<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
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
        if (!Schema::hasTable('certificates_training_activity_logs')) {
            return;
        }

        ActivityLog::create([
            'event' => $event,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'causer_id' => Auth::id(),
            'causer_name' => Auth::user() ? Auth::user()->name : null,
            'description' => $description,
            'properties' => $properties ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
        ]);
    }
}
