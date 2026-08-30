<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\PermissionService;
use Tests\CreatesInMemoryDatabase;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use CreatesInMemoryDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useInMemorySqlite();
    }

    public function test_super_admin_can_mutate_and_access_all_apps(): void
    {
        $user = new User(['is_super_admin' => true]);
        $user->id = 1;

        $service = app(PermissionService::class);

        $this->assertTrue($service->isSuperAdmin($user));
        $this->assertTrue($service->canMutate($user));
        $this->assertTrue($service->canAccessApp($user, 'inspection'));
    }

    public function test_view_only_user_cannot_mutate(): void
    {
        config(['cvs.app_key' => 'training']);

        $user = User::factory()->create([
            'is_super_admin' => false,
        ]);

        $user->appPermissions()->create([
            'app_key' => 'training',
            'access_level' => 'view',
        ]);

        $service = app(PermissionService::class);

        $this->assertTrue($service->canAccessApp($user));
        $this->assertFalse($service->canMutate($user));
    }
}
