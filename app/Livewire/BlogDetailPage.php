<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Services\SeoService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class BlogDetailPage extends Component
{
    public ?Blog $post = null;

    public string $slug = '';

    public function mount(string $slug): void
    {
        $blogCache = config('performance.cache.blog', []);
        $detailVersion = (int) Cache::get('cache.version.blog.detail', 1);

        $this->slug = $slug;
        $this->post = Cache::flexible("blog.v{$detailVersion}.post.{$slug}", [
            (int) ($blogCache['detail_fresh'] ?? 600),
            (int) ($blogCache['detail_stale'] ?? 1800),
        ], function () use ($slug): ?Blog {
            return Blog::query()
                ->select(['id', 'title', 'slug', 'excerpt', 'content', 'category', 'image', 'image_url', 'image_source', 'read_time', 'author', 'published_at', 'is_published', 'meta_title', 'meta_description', 'meta_keywords', 'created_at', 'updated_at'])
                ->where('slug', $slug)
                ->published()
                ->first();
        });

        if (! $this->post) {
            abort(404);
        }
    }

    public function render()
    {
        $blogCache = config('performance.cache.blog', []);
        $relatedVersion = (int) Cache::get('cache.version.blog.related', 1);

        $cachedRelated = Cache::flexible("blog.v{$relatedVersion}.related.".$this->post->category, [
            (int) ($blogCache['related_fresh'] ?? 600),
            (int) ($blogCache['related_stale'] ?? 1800),
        ], function (): Collection {
            return Blog::query()
                ->select(['id', 'title', 'slug', 'excerpt', 'category', 'image', 'image_url', 'read_time', 'author', 'published_at', 'created_at'])
                ->published()
                ->where('category', $this->post->category)
                ->orderBy('published_at', 'desc')
                ->limit(8)
                ->get();
        });

        $relatedPosts = $cachedRelated
            ->where('id', '!=', $this->post->id)
            ->take(2)
            ->values();

        $seoService = app(SeoService::class);

        $structuredData = [
            $seoService->generateBlogStructuredData($this->post),
            $seoService->generateBreadcrumbStructuredData([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Blog', 'url' => route('blog.index')],
                ['name' => $this->post->title, 'url' => route('blog.show', $this->post->slug)],
            ]),
        ];

        return view('livewire.blog-detail-page', [
            'relatedPosts' => $relatedPosts,
            'enableMathJax' => true,
            'structuredData' => $structuredData,
        ])->layout('layouts.app', [
            'enableMathJax' => true,
        ]);
    }
}
