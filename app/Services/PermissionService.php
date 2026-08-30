<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserAppPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

    public static function deniedMessage(string $type = 'mutate'): string
    {
        return match ($type) {
            'super_admin' => 'Only super administrators can access this area.',
            default => 'You have view-only access and cannot perform this action.',
        };
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
        $previous = $this->permissionMapFor($user);
        $normalized = $this->normalizePermissions($permissions);

        UserAppPermission::where('user_id', $user->id)->delete();

        foreach ($normalized as $appKey => $level) {
            UserAppPermission::create([
                'user_id' => $user->id,
                'app_key' => $appKey,
                'access_level' => $level,
            ]);
        }

        $this->forgetPermissionCache($user);

        if ($previous !== $normalized && Auth::check()) {
            app(ActivityLogService::class)->record(
                'user.permissions_updated',
                'user',
                $user->id,
                'Permissions for "' . $user->name . '" were updated by ' . Auth::user()->name . '.',
                [
                    'previous' => $previous,
                    'current' => $normalized,
                ]
            );
        }
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

        $this->forgetPermissionCache($user);
    }

    private function accessLevelFor(User $user, ?string $appKey = null): ?string
    {
        $appKey = $appKey ?: config('cvs.app_key');

        return $this->permissionMapFor($user)[$appKey] ?? null;
    }

    /** @return array<string, string> */
    private function permissionMapFor(User $user): array
    {
        if (isset($this->cache[$user->id])) {
            return $this->cache[$user->id];
        }

        $cacheKey = $this->permissionCacheKey($user->id);
        $ttl = config('cvs.cache_ttl.permissions', 900);

        $this->cache[$user->id] = Cache::remember($cacheKey, $ttl, function () use ($user) {
            return UserAppPermission::where('user_id', $user->id)
                ->pluck('access_level', 'app_key')
                ->all();
        });

        return $this->cache[$user->id];
    }

    /** @return array<string, string> */
    private function normalizePermissions(array $permissions): array
    {
        $normalized = [];

        foreach ($permissions as $appKey => $level) {
            if (!in_array($level, ['view', 'full'], true)) {
                continue;
            }

            if (!array_key_exists($appKey, config('cvs.apps', []))) {
                continue;
            }

            $normalized[$appKey] = $level;
        }

        ksort($normalized);

        return $normalized;
    }

    private function forgetPermissionCache(User $user): void
    {
        Cache::forget($this->permissionCacheKey($user->id));
        unset($this->cache[$user->id]);
    }

    private function permissionCacheKey(int $userId): string
    {
        return 'cvs.permissions.' . config('cvs.app_key') . '.' . $userId;
    }
}
