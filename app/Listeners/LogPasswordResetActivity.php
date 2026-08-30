<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\PasswordReset;

class LogPasswordResetActivity
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function handle(PasswordReset $event): void
    {
        $user = $event->user;

        $this->activityLog->record(
            'user.password_reset_completed',
            'user',
            $user->id,
            'User "' . $user->name . '" completed a password reset.'
        );
    }
}
