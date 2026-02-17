<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Ai\Agents\PortfolioAssistant;
use App\Ai\Tools\GetExperiencesTool;
use App\Ai\Tools\SearchBlogsTool;
use App\Ai\Tools\SearchProjectsTool;
use App\Livewire\AIChatWidget;
use Illuminate\Foundation\Testing\TestCase;
use Livewire\Livewire;

class AIAssistantTest extends TestCase
{
    public function test_portfolio_assistant_agent_exists(): void
    {
        $agent = new PortfolioAssistant;

        $this->assertInstanceOf(PortfolioAssistant::class, $agent);
    }

    public function test_portfolio_assistant_has_tools(): void
    {
        $agent = new PortfolioAssistant;
        $tools = $agent->tools();

        $this->assertCount(3, $tools);
    }

    public function test_portfolio_assistant_has_instructions(): void
    {
        $agent = new PortfolioAssistant;
        $instructions = $agent->instructions();

        $this->assertNotEmpty($instructions);
        $this->assertStringContainsString('Fatih', (string) $instructions);
    }

    public function test_chat_widget_livewire_component_can_be_rendered(): void
    {
        Livewire::test(AIChatWidget::class)
            ->assertOk()
            ->assertViewIs('livewire.ai-chat-widget');
    }

    public function test_chat_widget_toggles_state(): void
    {
        $component = Livewire::test(AIChatWidget::class);

        // Should not throw exception
        $component->call('toggle');
        $component->call('toggle');

        $this->assertTrue(true);
    }

    public function test_chat_widget_can_close(): void
    {
        Livewire::test(AIChatWidget::class)
            ->set('isOpen', true)
            ->call('close')
            ->assertSet('isOpen', false);
    }

    public function test_chat_widget_can_clear_chat(): void
    {
        Livewire::test(AIChatWidget::class)
            ->call('clearChat')
            ->assertHasNoErrors();
    }

    public function test_chat_widget_has_welcome_message(): void
    {
        $component = Livewire::test(AIChatWidget::class);
        $history = $component->chatHistory;

        $this->assertNotEmpty($history);
        $this->assertSame('assistant', $history[0]['role']);
        $this->assertStringContainsString('Fatih', $history[0]['content']);
    }

    public function test_search_projects_tool_exists(): void
    {
        $agent = new PortfolioAssistant;
        $tools = $agent->tools();

        $toolClasses = array_map(fn ($tool) => get_class($tool), $tools);
        $this->assertContains(SearchProjectsTool::class, $toolClasses);
    }

    public function test_search_blogs_tool_exists(): void
    {
        $agent = new PortfolioAssistant;
        $tools = $agent->tools();

        $toolClasses = array_map(fn ($tool) => get_class($tool), $tools);
        $this->assertContains(SearchBlogsTool::class, $toolClasses);
    }

    public function test_get_experiences_tool_exists(): void
    {
        $agent = new PortfolioAssistant;
        $tools = $agent->tools();

        $toolClasses = array_map(fn ($tool) => get_class($tool), $tools);
        $this->assertContains(GetExperiencesTool::class, $toolClasses);
    }

    public function test_portfolio_assistant_can_be_prompted(): void
    {
        PortfolioAssistant::fake(['This is a test response']);

        $agent = new PortfolioAssistant;
        $response = $agent->prompt('test message');

        $this->assertNotEmpty($response);
        $this->assertSame('This is a test response', (string) $response);
    }

    public function test_portfolio_assistant_can_assert_prompted(): void
    {
        PortfolioAssistant::fake(['Response']);

        $agent = new PortfolioAssistant;
        $agent->prompt('hello there');

        PortfolioAssistant::assertPrompted('hello there');
    }
}
