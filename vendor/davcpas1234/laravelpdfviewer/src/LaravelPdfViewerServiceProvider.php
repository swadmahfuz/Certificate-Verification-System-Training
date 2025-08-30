<?php

/*
 * This file is part of the Laravel PDF VIEWER package.
 *
 * (c) Gooodness Kayode <gtkbrain@gmail.com>
 * (c) David Passmore <david.passmore@verscreative.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Davcpas1234\LaravelPdfViewer;

use Illuminate\Support\ServiceProvider;

class LaravelPdfViewerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'laraview' => public_path('laraview'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
