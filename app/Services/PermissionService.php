<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAppPermission;
use Illuminate\Support\Facades\Auth;

class PermissionService
{
    /** @var array<int, array<string, string|null>> */
    private array $cache = [];

    public function isSuperAdmin(?User $user = null): bool
    {
        $user = $user ?: Auth::user();

        return $user ? (bool) $user->is_super_admin : false;
    }

    public function canManageUsers(?User $user = null): bool
    {
        return $this->isSuperAdmin($user);
    }

    public function canAccessApp(?User $user = null, ?string $appKey = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        if ($this->isSuperAdmin($user)) {
            return true;
        }

        $level = $this->accessLevelFor($user, $appKey);

        return in_array($level, ['view', 'full'], true);
    }

    public function canView(?User $user = null, ?string $appKey = null): bool
    {
        return $this->canAccessApp($user, $appKey);
    }

    public function canMutate(?User $user = null, ?string $appKey = null): bool
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return false;
        }

        if ($this->isSuperAdmin($user)) {
            return true;
        }

        return $this->accessLevelFor($user, $appKey) === 'full';
    }

    public function accessibleApps(?User $user = null): array
    {
        $user = $user ?: Auth::user();

        if (!$user) {
            return [];
        }

        if ($this->isSuperAdmin($user)) {
            return array_keys(config('cvs.apps', []));
        }

        $apps = [];

        foreach ($this->permissionMapFor($user) as $appKey => $level) {
            if (in_array($level, ['view', 'full'], true)) {
                $apps[] = $appKey;
            }
        }

        return $apps;
    }

    public function syncPermissions(User $user, array $permissions): void
    {
        UserAppPermission::where('user_id', $user->id)->delete();

        foreach ($permissions as $appKey => $level) {
            if (!in_array($level, ['view', 'full'], true)) {
                continue;
            }

            if (!array_key_exists($appKey, config('cvs.apps', []))) {
                continue;
            }

            UserAppPermission::create([
                'user_id' => $user->id,
                'app_key' => $appKey,
                'access_level' => $level,
            ]);
        }

        unset($this->cache[$user->id]);
    }

    public function grantDefaultRegistrationAccess(User $user, ?string $appKey = null): void
    {
        $appKey = $appKey ?: config('cvs.app_key');

        UserAppPermission::updateOrCreate(
            [
                'user_id' => $user->id,
                'app_key' => $appKey,
            ],
            [
                'access_level' => 'view',
            ]
        );

        unset($this->cache[$user->id]);
    }

    private function accessLevelFor(User $user, ?string $appKey = null): ?string
    {
        $appKey = $appKey ?: config('cvs.app_key');

        return $this->permissionMapFor($user)[$appKey] ?? null;
    }

    /** @return array<string, string> */
    private function permissionMapFor(User $user): array
    {
        if (!isset($this->cache[$user->id])) {
            $this->cache[$user->id] = UserAppPermission::where('user_id', $user->id)
                ->pluck('access_level', 'app_key')
                ->all();
        }

        return $this->cache[$user->id];
    }
}
