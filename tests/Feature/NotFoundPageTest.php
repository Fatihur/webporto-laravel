<?php

namespace Tests\Feature;

use Tests\TestCase;

class NotFoundPageTest extends TestCase
{
    public function test_unknown_route_returns_custom_404_page(): void
    {
        $response = $this->get('/this-page-does-not-exist-404');

        $response->assertStatus(404);
        $response->assertSee('Page Not Found');
        $response->assertSee('Back Home');
        $response->assertSee('View Projects');
        $response->assertSee('Browse Blog');
    }
}
