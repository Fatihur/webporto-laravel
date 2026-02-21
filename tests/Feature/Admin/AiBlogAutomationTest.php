<?php

namespace Tests\Feature\Admin;

use App\Models\AiBlogAutomation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AiBlogAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create());
    }

    #[Test]
    public function test_user_can_view_automation_index(): void
    {
        $response = $this->get(route('admin.ai-blog.index'));

        $response->assertStatus(200);
        $response->assertSee('AI Blog Automation');
    }

    #[Test]
    public function test_user_can_create_automation(): void
    {
        $response = $this->get(route('admin.ai-blog.create'));

        $response->assertStatus(200);
        $response->assertSee('New Automation');
    }

    #[Test]
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

        Livewire::test(\App\Livewire\Admin\AiBlog\Form::class)
            ->set('name', $data['name'])
            ->set('topic_prompt', $data['topic_prompt'])
            ->set('content_prompt', $data['content_prompt'])
            ->set('category', $data['category'])
            ->set('frequency', $data['frequency'])
            ->set('scheduled_at', $data['scheduled_at'])
            ->set('is_active', $data['is_active'])
            ->set('max_articles_per_day', $data['max_articles_per_day'])
            ->set('auto_publish', $data['auto_publish'])
            ->call('save')
            ->assertRedirect(route('admin.ai-blog.index'));

        $this->assertDatabaseHas('ai_blog_automations', [
            'name' => 'Test Automation',
            'category' => 'technology',
        ]);
    }

    #[Test]
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

    #[Test]
    public function test_automation_should_run_now_when_active(): void
    {
        $automation = AiBlogAutomation::factory()->create([
            'frequency' => 'daily',
            'is_active' => true,
            'next_run_at' => now()->subHour(),
        ]);

        $this->assertTrue($automation->shouldRunNow());
    }

    #[Test]
    public function test_automation_should_not_run_when_inactive(): void
    {
        $automation = AiBlogAutomation::factory()->create([
            'frequency' => 'daily',
            'is_active' => false,
            'next_run_at' => now()->subHour(),
        ]);

        $this->assertFalse($automation->shouldRunNow());
    }

    #[Test]
    public function test_user_can_view_logs_page(): void
    {
        $response = $this->get(route('admin.ai-blog.logs'));

        $response->assertStatus(200);
        $response->assertSee('Generation Logs');
    }

    #[Test]
    public function test_user_can_view_dashboard(): void
    {
        $response = $this->get(route('admin.knowledge.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('AI Blog Dashboard');
    }
}
