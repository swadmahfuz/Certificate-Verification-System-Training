<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;

class EnsureSuperAdmin
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$this->permissions->canManageUsers($user)) {
            abort(403, 'Super Admin access is required.');
        }

        return $next($request);
    }
}
