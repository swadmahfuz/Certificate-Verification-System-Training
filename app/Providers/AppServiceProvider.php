<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * Fully rewritten to:
     *  - Keep your existing Bootstrap pagination + default string length.
     *  - Dynamically set the session cookie domain so SSO works across subdomains
     *    without hard-coding SESSION_DOMAIN in .env.
     *  - Respect an explicitly set SESSION_DOMAIN (if you set it in .env).
     *  - Handle localhost / IPs gracefully by not forcing a shared domain.
     *  - Handle common ccTLD patterns (e.g., example.com.bd, example.co.uk).
     *  - Ensure a shared cookie name across apps if the default is still used.
     *
     * Requirements for cross-subdomain login:
     *  - Same APP_KEY across all apps.
     *  - Same session driver and same session store (e.g., database or Redis).
     *  - Same cookie name across apps.
     *  - HTTPS recommended with SESSION_SECURE_COOKIE=true.
     *
     * @return void
     */
    public function boot()
    {
        // Keep your existing UI/database defaults
        Paginator::useBootstrap();
        Schema::defaultStringLength(191);

        // If SESSION_DOMAIN is explicitly set in .env (and thus in config), respect it.
        if (config('session.domain') !== null) {
            $this->ensureSharedCookieName();
            return;
        }

        // Avoid overriding during console tasks with no HTTP request context
        // (e.g., artisan, queue workers not handling an HTTP request).
        if (app()->runningInConsole()) {
            $this->ensureSharedCookieName();
            return;
        }

        // Try to detect the current host from the incoming HTTP request.
        $host = null;
        try {
            $host = request()->getHost(); // e.g. inspection.tuvbd.com.bd
        } catch (\Throwable $e) {
            // No request available (edge case). Leave as-is.
        }

        if ($host) {
            // If localhost or an IP address, don't force a shared domain.
            $isLocalhost = ($host === 'localhost');
            $isIp        = filter_var($host, FILTER_VALIDATE_IP) !== false;

            if (! $isLocalhost && ! $isIp) {
                $parts = explode('.', $host);
                $count = count($parts);

                // Derive the registrable base domain.
                // Default to full host (safe fallback).
                $base = $host;

                if ($count > 2) {
                    $tld = $parts[$count - 1];   // e.g. bd / uk / com
                    $sld = $parts[$count - 2];   // e.g. com / co / example

                    // Common second-level labels under ccTLDs: com.bd, co.uk, org.uk, etc.
                    $twoLevelSet = ['co', 'com', 'org', 'net', 'gov', 'edu', 'ac'];

                    if (strlen($tld) === 2 && in_array($sld, $twoLevelSet, true)) {
                        // ccTLD with second-level label (example.com.bd → take last 3 labels)
                        $base = implode('.', array_slice($parts, -3));
                    } else {
                        // Standard domain (example.com → take last 2 labels)
                        $base = implode('.', array_slice($parts, -2));
                    }
                }

                // Prepend a dot to share the cookie across all subdomains
                Config::set('session.domain', '.' . $base);
            } else {
                // Localhost/IP: leave null (host-only cookie; no cross-subdomain sharing)
                Config::set('session.domain', null);
            }
        }

        // Ensure we use a shared, explicit cookie name if the default is still in place.
        $this->ensureSharedCookieName();
    }

    /**
     * Ensure all apps use the same cookie name so the browser presents
     * the same session cookie to each subdomain application.
     */
    protected function ensureSharedCookieName(): void
    {
        // If SESSION_COOKIE is defined, respect it; otherwise set a shared default.
        Config::set('session.cookie', env('SESSION_COOKIE', 'cvs_session'));
    }
}
