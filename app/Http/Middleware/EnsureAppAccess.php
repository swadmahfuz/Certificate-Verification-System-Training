<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;

class EnsureAppAccess
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$this->permissions->canAccessApp($user)) {
            return redirect()->route('no-access');
        }

        return $next($request);
    }
}
