<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class AuthRedirectTest extends TestCase
{
    public function test_guest_is_redirected_to_admin_login_for_protected_admin_routes(): void
    {
        $dashboardResponse = $this->get('/admin');
        $dashboardResponse->assertRedirect('/admin/login');
    }
}
