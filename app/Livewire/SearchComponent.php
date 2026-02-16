<?php

namespace App\Livewire;

use App\Models\Blog;
use App\Models\Project;
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
        $this->results = [];

        if ($this->type === 'all' || $this->type === 'projects') {
            $projects = Project::search($this->query)
                ->take(5)
                ->get();

            foreach ($projects as $project) {
                $this->results[] = [
                    'type' => 'project',
                    'data' => $project,
                ];
            }
        }

        if ($this->type === 'all' || $this->type === 'blogs') {
            $blogs = Blog::search($this->query)
                ->query(function ($builder) {
                    $builder->published();
                })
                ->take(5)
                ->get();

            foreach ($blogs as $blog) {
                $this->results[] = [
                    'type' => 'blog',
                    'data' => $blog,
                ];
            }
        }

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
