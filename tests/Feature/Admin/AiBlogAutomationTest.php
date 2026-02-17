<?php

namespace Tests\Feature\Admin;

use App\Models\AiBlogAutomation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiBlogAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    public function test_user_can_view_automation_index(): void
    {
        $response = $this->get(route('admin.ai-blog.index'));

        $response->assertStatus(200);
        $response->assertSee('AI Blog Automation');
    }

    public function test_user_can_create_automation(): void
    {
        $response = $this->get(route('admin.ai-blog.create'));

        $response->assertStatus(200);
        $response->assertSee('New Automation');
    }

    public function test_user_can_store_automation(): void
    {
        $data = [
            'name' => 'Test Automation',
            'topic_prompt' => 'Write about Laravel tips and tricks',
            'content_prompt' => 'Make it beginner friendly with examples',
            'category' => 'technology',
            'frequency' => 'daily',
            'scheduled_at' => '09:00',
            'is_active' => true,
            'max_articles_per_day' => 1,
            'auto_publish' => true,
        ];

        $response = $this->post(route('admin.ai-blog.create'), $data);

        $response->assertRedirect(route('admin.ai-blog.index'));
        $this->assertDatabaseHas('ai_blog_automations', [
            'name' => 'Test Automation',
            'category' => 'technology',
        ]);
    }

    public function test_automation_calculates_next_run_correctly(): void
    {
        $automation = AiBlogAutomation::factory()->create([
            'frequency' => 'daily',
            'scheduled_at' => '09:00:00',
            'is_active' => true,
        ]);

        $nextRun = $automation->calculateNextRun();

        $this->assertNotNull($nextRun);
        $this->assertEquals('09:00:00', $nextRun->format('H:i:s'));
    }

    public function test_automation_should_run_now_when_active(): void
    {
        $automation = AiBlogAutomation::factory()->create([
            'frequency' => 'daily',
            'is_active' => true,
            'next_run_at' => now()->subHour(),
        ]);

        $this->assertTrue($automation->shouldRunNow());
    }

    public function test_automation_should_not_run_when_inactive(): void
    {
        $automation = AiBlogAutomation::factory()->create([
            'frequency' => 'daily',
            'is_active' => false,
            'next_run_at' => now()->subHour(),
        ]);

        $this->assertFalse($automation->shouldRunNow());
    }

    public function test_user_can_view_logs_page(): void
    {
        $response = $this->get(route('admin.ai-blog.logs'));

        $response->assertStatus(200);
        $response->assertSee('Generation Logs');
    }

    public function test_user_can_view_dashboard(): void
    {
        $response = $this->get(route('admin.ai-blog.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('AI Blog Dashboard');
    }
}
