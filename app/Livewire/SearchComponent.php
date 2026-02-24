<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class SearchComponent extends Component
{
    use WithPagination;

    public string $query = '';

    public string $type = 'all'; // all, projects, blogs

    public array $results = [];

    public bool $isSearching = false;

    public function updatedQuery()
    {
        $this->resetPage();

        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        $this->performSearch();
    }

    public function updatedType()
    {
        if (strlen($this->query) >= 2) {
            $this->performSearch();
        }
    }

    public function performSearch()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];

            return;
        }

        $this->isSearching = true;
        $normalizedQuery = mb_strtolower(trim($this->query));
        $searchVersionBlogs = (int) Cache::get('cache.version.search.blogs', 1);
        $searchVersionProjects = (int) Cache::get('cache.version.search.projects', 1);
        $ttlSeconds = (int) config('performance.cache.search.results_seconds', 120);

        $results = [];

        if ($this->type === 'all' || $this->type === 'projects') {
            $projects = Cache::remember(
                'search.v'.$searchVersionProjects.'.projects.'.md5($normalizedQuery),
                now()->addSeconds($ttlSeconds),
                function () use ($normalizedQuery) {
                    return Project::search($normalizedQuery)
                        ->take(5)
                        ->get();
                }
            );

            foreach ($projects as $project) {
                $results[] = [
                    'type' => 'project',
                    'data' => $project,
                ];
            }
        }

        if ($this->type === 'all' || $this->type === 'blogs') {
            $blogs = Cache::remember(
                'search.v'.$searchVersionBlogs.'.blogs.'.md5($normalizedQuery),
                now()->addSeconds($ttlSeconds),
                function () use ($normalizedQuery) {
                    return Blog::search($normalizedQuery)
                        ->query(function ($builder) {
                            $builder->published();
                        })
                        ->take(5)
                        ->get();
                }
            );

            foreach ($blogs as $blog) {
                $results[] = [
                    'type' => 'blog',
                    'data' => $blog,
                ];
            }
        }

        $this->results = $results;

        $this->isSearching = false;
    }

    // Keep for backward compatibility
    public function search()
    {
        $this->performSearch();
    }

    public function render()
    {
        return view('livewire.search-component');
    }
}
