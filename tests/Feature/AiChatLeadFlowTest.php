<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Ai\Agents\PortfolioAssistant;
use App\Livewire\AiChatWidget;
use App\Models\Contact;
use App\Services\AiChatRetrievalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Scout\EngineManager;
use Livewire\Livewire;
use Tests\TestCase;

class AiChatLeadFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_collects_lead_data_and_creates_contact_from_chat(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $component = Livewire::test(AiChatWidget::class);

        $component->call('processMessage', 'Saya mau kerja sama bikin website company profile');
        $component->assertSet('leadMode', true);

        $component->call('processMessage', 'Budi');
        $component->call('processMessage', 'budi@example.com');
        $component->call('processMessage', 'software_dev');
        $component->call('processMessage', '30 juta');
        $component->call('processMessage', '4 minggu');

        $this->assertDatabaseHas('contacts', [
            'name' => 'Budi',
            'email' => 'budi@example.com',
            'subject' => 'AI Lead: software_dev',
        ]);

        $this->assertSame(1, Contact::count());
    }

    public function test_it_appends_citations_when_retrieval_sources_exist(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $retrievalMock = $this->mock(AiChatRetrievalService::class);
        $retrievalMock->shouldReceive('retrieve')
            ->once()
            ->andReturn([
                'sources' => [
                    [
                        'type' => 'blog',
                        'title' => 'Laravel SEO Guide',
                        'url' => 'https://example.com/blog/laravel-seo-guide',
                        'snippet' => 'Panduan SEO Laravel.',
                    ],
                ],
            ]);
        $retrievalMock->shouldReceive('buildPromptContext')->andReturn('retrieval context');
        $retrievalMock->shouldReceive('formatCitationBlock')->andReturn("📎 **Sumber rujukan:**\n• [Laravel SEO Guide](https://example.com/blog/laravel-seo-guide)");

        PortfolioAssistant::fake(['Ini jawaban dari AI.']);

        $component = Livewire::test(AiChatWidget::class);
        $component->call('processMessage', 'seo laravel services');

        $history = $component->get('chatHistory');
        $lastAssistant = collect($history)->reverse()->first(fn (array $entry): bool => $entry['role'] === 'assistant');

        $this->assertNotNull($lastAssistant);
        $this->assertStringContainsString('Sumber rujukan', $lastAssistant['content']);
    }
}
