<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Livewire\AIChatWidget;
use PHPUnit\Framework\Attributes\Test;
use ReflectionClass;
use Tests\TestCase;

class AiChatFallbackMessageTest extends TestCase
{
    #[Test]
    public function it_returns_helpful_fallback_response_for_general_question(): void
    {
        $widget = new AIChatWidget;

        $reflection = new ReflectionClass($widget);
        $method = $reflection->getMethod('generateDemoResponse');
        $method->setAccessible(true);

        $response = $method->invoke($widget, 'saya butuh bantuan');

        $this->assertIsString($response);
        $this->assertStringContainsString('layanan AI sedang sementara tidak tersedia', $response);
    }
}
