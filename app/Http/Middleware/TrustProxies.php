<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    protected $proxies = '*'; // trust all (shared hosting friendly)

    protected $headers = Request::HEADER_X_FORWARDED_ALL;
    // If you're behind Cloudflare, you can also use:
    // protected $headers = Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PORT | Request::HEADER_X_FORWARDED_FOR;
}
