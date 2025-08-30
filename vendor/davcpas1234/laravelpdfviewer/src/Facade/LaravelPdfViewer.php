<?php

/*
 * This file is part of the Laravel Pdf Viewer package.
 *
 * (c) Gooodness Toluwanimi Kayode <gtkbrain@gmail.com>
 * (c) David Passmore <david.passmore@verscreative.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Davcpas1234\LaravelPdfViewer\Facade;
use Illuminate\Support\Facades\Facade;

class LaravelPdfViewer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravelpdfviewer';
    }
}
