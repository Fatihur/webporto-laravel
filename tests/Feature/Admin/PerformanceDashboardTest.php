<?php

namespace Tests\Feature\Admin;

use App\Models\AiBlogAutomation;
use App\Models\AiBlogLog;
use App\Models\User;
use App\Models\WebVitalMetric;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PerformanceDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_admin_can_view_performance_dashboard_with_metrics(): void
    {
        $user = User::factory()->create();
        $automation = AiBlogAutomation::factory()->create();

        WebVitalMetric::query()->create([
            'path' => '/',
            'metric' => 'LCP',
            'value' => 2100,
            'rating' => 'good',
            'page_group' => 'home',
            'device_type' => 'desktop',
            'connection_type' => '4g',
            'user_agent_hash' => hash('sha256', 'test-agent'),
            'recorded_at' => now(),
        ]);

        AiBlogLog::query()->create([
            'ai_blog_automation_id' => $automation->id,
            'status' => 'failed',
            'error_message' => 'Rate limited by provider',
            'started_at' => now()->subSeconds(40),
            'completed_at' => now(),
        ]);

        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => '{}',
            'attempts' => 0,
            'available_at' => now()->timestamp,
            'created_at' => now()->timestamp,
        ]);

        DB::table('failed_jobs')->insert([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'connection' => 'database',
            'queue' => 'default',
            'payload' => '{}',
            'exception' => 'Sample exception',
            'failed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('admin.performance.dashboard'));

        $response->assertOk();
        $response->assertSee('Performance & Reliability', false);
        $response->assertSee('Core Web Vitals');
        $response->assertSee('Queue Health');
        $response->assertSee('Cache Strategy Snapshot');
    }

    #[Test]
    public function guest_is_redirected_from_performance_dashboard(): void
    {
        $response = $this->get(route('admin.performance.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }
}
