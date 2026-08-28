<?php

namespace App\Listeners;

use App\Services\ActivityLogService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Password;

class SendPasswordSetupLinkAfterVerification
{
    public function __construct(private ActivityLogService $activityLog)
    {
    }

    public function handle(Verified $event): void
    {
        $user = $event->user;

        if (!$user->mustChangePassword()) {
            return;
        }

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->activityLog->record(
                'user.password_setup_sent',
                'user',
                $user->id,
                'Password setup email sent to "' . $user->name . '" after email verification.'
            );
        }
    }
}
