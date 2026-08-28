<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;

class NoAccessController extends Controller
{
    public function __invoke(PermissionService $permissions)
    {
        return view('no-access', [
            'accessibleApps' => $permissions->accessibleApps(),
        ]);
    }
}
