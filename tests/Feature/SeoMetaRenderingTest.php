<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Scout\EngineManager;
use Tests\TestCase;

class SeoMetaRenderingTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_default_meta_and_structured_data(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical" href="'.route('home').'">', false);
        $response->assertSee('application/ld+json', false);
        $response->assertSee('WebSite');
    }

    public function test_blog_detail_page_renders_article_meta_tags(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $blog = Blog::factory()->published()->create([
            'slug' => 'seo-article-page',
            'meta_title' => 'SEO Article',
            'meta_description' => 'An SEO focused article.',
        ]);

        $response = $this->get(route('blog.show', $blog->slug));

        $response->assertOk();
        $response->assertSee('<meta property="og:type" content="article">', false);
        $response->assertSee('article:published_time', false);
        $response->assertSee('BlogPosting');
        $response->assertSee(route('blog.show', $blog->slug), false);
    }

    public function test_sitemap_route_has_cache_header(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        Project::factory()->create();
        Blog::factory()->published()->create();

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('public', $cacheControl);
        $this->assertStringContainsString('max-age=21600', $cacheControl);
    }
}
