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

    public function test_it_prioritizes_highly_relevant_sources(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        KnowledgeEntry::factory()->create([
            'title' => 'SEO Basic Tips',
            'content' => 'Tips SEO dasar untuk pemula.',
            'category' => 'general',
            'is_active' => true,
        ]);

        Blog::factory()->published()->create([
            'title' => 'Website Company Profile Update',
            'slug' => 'website-company-profile-update',
            'excerpt' => 'Update ringan website company profile.',
            'published_at' => now(),
        ]);

        Blog::factory()->published()->create([
            'title' => 'Laravel SEO Optimization Guide',
            'slug' => 'laravel-seo-optimization-guide',
            'excerpt' => 'Panduan optimasi SEO Laravel dari sisi teknis.',
            'published_at' => now()->subDay(),
        ]);

        $service = new AiChatRetrievalService;
        $result = $service->retrieve('laravel seo optimization', 1);

        $this->assertCount(1, $result['sources']);
        $this->assertSame('Laravel SEO Optimization Guide', $result['sources'][0]['title']);
    }

    public function test_it_applies_pricing_intent_filter_to_prioritize_pricing_knowledge(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        KnowledgeEntry::query()->create([
            'title' => 'Harga Website Company Profile',
            'content' => 'Range harga mulai 15 juta tergantung scope.',
            'category' => 'pricing',
            'tags' => ['harga', 'pricing', 'website'],
            'is_active' => true,
            'usage_count' => 0,
        ]);

        KnowledgeEntry::query()->create([
            'title' => 'Tips Design Branding',
            'content' => 'Panduan umum branding visual.',
            'category' => 'skills',
            'tags' => ['design', 'branding'],
            'is_active' => true,
            'usage_count' => 0,
        ]);

        Blog::factory()->published()->create([
            'title' => 'General Portfolio Update',
            'slug' => 'general-portfolio-update',
            'excerpt' => 'Update umum portfolio.',
        ]);

        Project::factory()->create([
            'title' => 'Harga Website Company Profile',
            'slug' => 'harga-website-company-profile',
            'description' => 'Project showcase website company profile.',
            'category' => 'software-dev',
        ]);

        $service = new AiChatRetrievalService;
        $result = $service->retrieve('berapa harga website company profile', 1);

        $this->assertCount(1, $result['sources']);
        $this->assertSame('knowledge', $result['sources'][0]['type']);
        $this->assertSame('Harga Website Company Profile', $result['sources'][0]['title']);
    }
}
