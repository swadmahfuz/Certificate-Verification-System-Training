<?php

namespace App\Providers;

use App\Listeners\LogAuthenticationActivity;
use App\Listeners\LogPasswordResetActivity;
use App\Listeners\LogUserRegistrationActivity;
use App\Listeners\SendPasswordSetupLinkAfterVerification;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            LogUserRegistrationActivity::class,
        ],
        Verified::class => [
            SendPasswordSetupLinkAfterVerification::class,
        ],
        Login::class => [
            LogAuthenticationActivity::class . '@handleLogin',
        ],
        Logout::class => [
            LogAuthenticationActivity::class . '@handleLogout',
        ],
        PasswordReset::class => [
            LogPasswordResetActivity::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
