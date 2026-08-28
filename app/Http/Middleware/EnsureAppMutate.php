<?php

namespace App\Http\Middleware;

use App\Services\PermissionService;
use Closure;
use Illuminate\Http\Request;

class EnsureAppMutate
{
    public function __construct(private PermissionService $permissions)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$this->permissions->canMutate($user)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
