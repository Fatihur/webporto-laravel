<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Livewire\AIChatWidget;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\TestCase;

class AiChatFallbackProvidersTest extends TestCase
{
    #[Test]
    public function it_builds_secondary_provider_candidates_from_config(): void
    {
        config([
            'ai.providers.openrouter.key' => 'openrouter-test-key',
            'ai.providers.openrouter.url' => 'https://openrouter.ai/api/v1',
            'ai.fallback.openrouter_model' => 'openai/gpt-4o-mini',
            'ai.providers.openai.key' => 'openai-test-key',
            'ai.providers.openai.url' => 'https://api.moonshot.cn/v1',
            'ai.fallback.openai_model' => 'moonshot-v1-8k',
        ]);

        $widget = new AIChatWidget;
        $reflection = new ReflectionClass($widget);
        $method = $reflection->getMethod('secondaryProviderCandidates');
        $method->setAccessible(true);

        $candidates = $method->invoke($widget);

        $this->assertIsArray($candidates);
        $this->assertCount(2, $candidates);
        $this->assertSame('openrouter', $candidates[0]['provider']);
        $this->assertSame('openai', $candidates[1]['provider']);
    }

    #[Test]
    public function secondary_system_prompt_keeps_fallback_answer_within_portfolio_scope(): void
    {
        $widget = new AIChatWidget;
        $reflection = new ReflectionClass($widget);
        $method = $reflection->getMethod('secondarySystemPrompt');
        $method->setAccessible(true);

        $prompt = $method->invoke($widget);

        $this->assertIsString($prompt);
        $this->assertStringContainsString('fokus hanya pada konteks portfolio Fatih', $prompt);
    }
}
