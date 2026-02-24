<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Services\SeoService;
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
        $blogCache = config('performance.cache.blog', []);
        $blogListVersion = (int) Cache::get('cache.version.blog.list', 1);

        if (! empty($this->search) || ! empty($this->category)) {
            $cacheKey = 'blog.v'.$blogListVersion.'.search.'.md5($this->search.'.'.$this->category.'.'.$page);
            $freshTtl = (int) ($blogCache['list_filtered_fresh'] ?? 180);
            $staleTtl = (int) ($blogCache['list_filtered_stale'] ?? 600);
        } else {
            $cacheKey = "blog.v{$blogListVersion}.posts.page.{$page}";
            $freshTtl = (int) ($blogCache['list_default_fresh'] ?? 600);
            $staleTtl = (int) ($blogCache['list_default_stale'] ?? 1800);
        }

        $posts = Cache::flexible($cacheKey, [$freshTtl, $staleTtl], function () {
            return $this->fetchPosts();
        });

        $categories = Cache::flexible('blog.v'.$blogListVersion.'.categories', [
            (int) ($blogCache['categories_fresh'] ?? 1800),
            (int) ($blogCache['categories_stale'] ?? 21600),
        ], function () {
            return Blog::published()
                ->distinct()
                ->pluck('category');
        });

        $seoService = app(SeoService::class);

        $structuredData = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => 'Blog',
                'url' => route('blog.index'),
                'description' => 'Kumpulan artikel tentang teknologi, desain, dan proses kreatif.',
            ],
            $seoService->generateBreadcrumbStructuredData([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => 'Blog', 'url' => route('blog.index')],
            ]),
        ];

        return view('livewire.blog-page', [
            'posts' => $posts,
            'categories' => $categories,
            'structuredData' => $structuredData,
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
            $searchTerm = '%'.strtolower($this->search).'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                    ->orWhere('excerpt', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm);
            });
        }

        return $query->paginate(9);
    }
}
