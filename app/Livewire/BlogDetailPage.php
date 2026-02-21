<?php

namespace App\Livewire;

use App\Models\Blog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class BlogDetailPage extends Component
{
    public ?Blog $post = null;

    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->post = Cache::flexible('blog.post.'.$slug, [300, 1800], function () use ($slug): ?Blog {
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
        $cachedRelated = Cache::flexible('blog.related.'.$this->post->category, [300, 1800], function (): Collection {
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

        return view('livewire.blog-detail-page', [
            'relatedPosts' => $relatedPosts,
            'enableMathJax' => true,
        ])->layout('layouts.app');
    }
}
