<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Project;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Scout\EngineManager;
use Tests\TestCase;

class SeoServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_generation_uses_valid_named_routes_and_content_urls(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        Blog::factory()->published()->create([
            'slug' => 'seo-blog-post',
            'image_url' => 'https://images.example.com/blog-cover.jpg',
        ]);

        Project::factory()->create([
            'slug' => 'seo-project',
            'thumbnail' => 'projects/thumbnails/seo-project.webp',
        ]);

        $service = app(SeoService::class);
        $sitemap = $service->generateSitemap();

        $this->assertStringContainsString(route('home'), $sitemap);
        $this->assertStringContainsString(route('projects.category', 'graphic-design'), $sitemap);
        $this->assertStringContainsString(route('blog.index'), $sitemap);
        $this->assertStringContainsString(route('contact.index'), $sitemap);
        $this->assertStringContainsString(route('blog.show', 'seo-blog-post'), $sitemap);
        $this->assertStringContainsString(route('projects.show', 'seo-project'), $sitemap);
        $this->assertStringContainsString('blog-cover.jpg', $sitemap);
        $this->assertStringContainsString('seo-project.webp', $sitemap);
    }

    public function test_website_structured_data_uses_blog_index_search_target(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $service = app(SeoService::class);
        $data = $service->generateWebsiteStructuredData();

        $this->assertArrayHasKey('potentialAction', $data);
        $this->assertSame('SearchAction', $data['potentialAction']['@type']);
        $this->assertStringContainsString(route('blog.index'), $data['potentialAction']['target']);
        $this->assertStringContainsString('search={search_term_string}', $data['potentialAction']['target']);
    }

    public function test_sitemap_is_cached_after_first_generation(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $service = app(SeoService::class);

        $first = $service->getCachedSitemap();
        $this->assertStringContainsString('<urlset', $first);

        $service->clearSitemapCache();
        $second = $service->getCachedSitemap();

        $this->assertStringContainsString('<urlset', $second);
    }
}
