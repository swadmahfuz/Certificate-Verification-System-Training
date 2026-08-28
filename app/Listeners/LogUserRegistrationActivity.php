<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Registered;

class LogUserRegistrationActivity
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function handle(Registered $event): void
    {
        $user = $event->user;

        $this->activityLog->record(
            'user.registered',
            'user',
            $user->id,
            'User "' . $user->name . '" registered.'
        );
    }
}
