<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class LogAuthenticationActivity
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function handleLogin(Login $event): void
    {
        $user = $event->user;

        $this->activityLog->record(
            'auth.login',
            'auth',
            $user->id,
            $user->name . ' logged in.'
        );
    }

    public function handleLogout(Logout $event): void
    {
        $user = $event->user;

        if (!$user) {
            return;
        }

        $this->activityLog->record(
            'auth.logout',
            'auth',
            $user->id,
            $user->name . ' logged out.'
        );
    }
}
