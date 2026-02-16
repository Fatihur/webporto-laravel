<?php

namespace App\Livewire;

use App\Models\Blog;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BlogPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'tailwind';

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $category = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->category = '';
        $this->resetPage();
    }

    public function render()
    {
        $page = $this->getPage();

        // Create cache key based on search, category, and page
        if (!empty($this->search) || !empty($this->category)) {
            // Cache filtered results for 2 minutes (shorter TTL for dynamic content)
            $cacheKey = 'blog.search.' . md5($this->search . '.' . $this->category . '.' . $page);
            $ttl = 120;
        } else {
            // Cache default pagination for 15 minutes
            $cacheKey = "blog.posts.page.{$page}";
            $ttl = 900;
        }

        $posts = Cache::remember($cacheKey, $ttl, function () {
            return $this->fetchPosts();
        });

        // Cache categories list separately
        $categories = Cache::remember('blog.categories', 3600, function () {
            return Blog::published()
                ->distinct()
                ->pluck('category');
        });

        return view('livewire.blog-page', [
            'posts' => $posts,
            'categories' => $categories,
        ])->layout('layouts.app');
    }

    private function fetchPosts()
    {
        $query = Blog::published()
            ->orderBy('published_at', 'desc');

        // Filter by category
        if ($this->category) {
            $query->byCategory($this->category);
        }

        // Filter by search term (database LIKE query)
        if ($this->search) {
            $searchTerm = '%' . strtolower($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('excerpt', 'like', $searchTerm)
                  ->orWhere('content', 'like', $searchTerm);
            });
        }

        return $query->paginate(9);
    }
}
