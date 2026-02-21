<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\Blog;
use App\Models\KnowledgeEntry;
use App\Models\Project;
use App\Services\AiChatRetrievalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Scout\EngineManager;
use Tests\TestCase;

class AiChatRetrievalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_collects_retrieval_sources_from_multiple_content_types(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        KnowledgeEntry::factory()->create([
            'title' => 'Web Development Service',
            'content' => 'Kami mengerjakan website company profile dan e-commerce.',
            'category' => 'services',
            'is_active' => true,
        ]);

        Blog::factory()->published()->create([
            'title' => 'Laravel Website Optimization',
            'slug' => 'laravel-website-optimization',
            'excerpt' => 'Optimasi website Laravel untuk performa dan SEO.',
        ]);

        Project::factory()->create([
            'title' => 'Company Profile Website',
            'slug' => 'company-profile-website',
            'description' => 'Pembuatan company profile website modern.',
            'category' => 'software-dev',
        ]);

        $service = new AiChatRetrievalService;
        $result = $service->retrieve('website company profile laravel');

        $this->assertNotEmpty($result);
        $this->assertArrayHasKey('sources', $result);
        $this->assertNotEmpty($result['sources']);
        $this->assertNotEmpty($service->buildPromptContext($result));
    }

    public function test_it_formats_citation_block_for_assistant_response(): void
    {
        $service = new AiChatRetrievalService;

        $result = [
            'sources' => [
                [
                    'type' => 'blog',
                    'title' => 'SEO Guide',
                    'url' => 'https://example.com/blog/seo-guide',
                    'snippet' => 'SEO guide content',
                ],
                [
                    'type' => 'project',
                    'title' => 'Portfolio Project',
                    'url' => 'https://example.com/project/portfolio-project',
                    'snippet' => 'Project snippet',
                ],
            ],
        ];

        $citation = $service->formatCitationBlock($result);

        $this->assertStringContainsString('Sumber rujukan', $citation);
        $this->assertStringContainsString('SEO Guide', $citation);
        $this->assertStringContainsString('Portfolio Project', $citation);
    }
}
