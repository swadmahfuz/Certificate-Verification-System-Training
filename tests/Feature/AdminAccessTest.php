<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    /**
     * @dataProvider protectedRoutes
     */
    public function test_admin_pages_require_authentication(string $uri)
    {
        $this->get($uri)->assertRedirect('/login');
    }

    public function protectedRoutes()
    {
        return [
            ['/dashboard'],
            ['/certificates'],
            ['/pending-certificates'],
            ['/activity-log'],
            ['/trainers'],
            ['/signatories'],
        ];
    }
}
