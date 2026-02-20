<?php

namespace Tests\Feature\Admin;

use App\Models\Blog;
use App\Models\Contact;
use App\Models\Experience;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\EngineManager;
use Tests\TestCase;

class DashboardCachingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_cached_stats_key(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        Project::factory()->count(2)->create();
        Blog::factory()->count(3)->create();
        Contact::factory()->count(4)->create();
        Experience::factory()->count(5)->create();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->assertNotNull(Cache::get('admin.dashboard.stats'));
    }
}
