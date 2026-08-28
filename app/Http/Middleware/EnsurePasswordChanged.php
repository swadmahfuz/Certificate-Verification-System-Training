<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsurePasswordChanged
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->mustChangePassword()) {
            if (!$request->routeIs('account.password.*', 'logout', 'verification.*', 'password.*')) {
                return redirect()->route('account.password.edit')
                    ->with('warning', 'You must set a new password before continuing.');
            }
        }

        return $next($request);
    }
}
