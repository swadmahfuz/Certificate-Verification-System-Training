<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\CreatesInMemoryDatabase;
use Tests\TestCase;

class LoginSecurityTest extends TestCase
{
    use CreatesInMemoryDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->useInMemorySqlite();
        $this->createActivityLogTables(['training']);
    }

    public function test_failed_login_is_throttled_after_repeated_attempts(): void
    {
        User::factory()->create([
            'email' => 'staff@example.com',
            'password' => Hash::make('CorrectPassword1'),
            'email_verified_at' => now(),
            'password_must_change' => false,
            'is_active' => true,
        ]);

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('certificate.login'), [
                'email' => 'staff@example.com',
                'password' => 'WrongPassword1',
            ]);
        }

        $response = $this->post(route('certificate.login'), [
            'email' => 'staff@example.com',
            'password' => 'WrongPassword1',
        ]);

        $response->assertStatus(429);
    }
}
