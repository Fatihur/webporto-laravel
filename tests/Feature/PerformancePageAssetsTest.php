<?php

namespace Tests\Feature;

use App\Models\Blog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Scout\EngineManager;
use Tests\TestCase;

class PerformancePageAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_does_not_load_mathjax_script(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('MathJax-script', false);
    }

    public function test_blog_detail_page_loads_mathjax_script(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $blog = Blog::factory()->published()->create([
            'slug' => 'math-content-blog',
            'content' => '<p>Formula: $E = mc^2$</p>',
        ]);

        $response = $this->get(route('blog.show', $blog->slug));

        $response->assertOk();
        $response->assertSee('MathJax-script', false);
    }

    public function test_blog_detail_page_renders_table_of_contents_markup(): void
    {
        app(EngineManager::class)->forgetEngines();
        config(['scout.driver' => 'null']);

        $blog = Blog::factory()->published()->create([
            'slug' => 'toc-blog',
            'content' => '<h2>Overview</h2><p>Sample paragraph.</p><h3>Details</h3><p>More details.</p>',
        ]);

        $response = $this->get(route('blog.show', $blog->slug));

        $response->assertOk();
        $response->assertSee('Table of Contents');
        $response->assertSee("window.dispatchEvent(new CustomEvent('toc-generated'", false);
    }
}
