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

    public function updatedQuery()
    {
        $this->resetPage();
    }

    public function search()
    {
        if (strlen($this->query) < 3) {
            $this->results = [];
            return;
        }

        $this->results = [];

        if ($this->type === 'all' || $this->type === 'projects') {
            $projects = Project::search($this->query)
                ->query(function ($builder) {
                    $builder->with('translations');
                })
                ->take(10)
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
                    $builder->published()->with('translations');
                })
                ->take(10)
                ->get();

            foreach ($blogs as $blog) {
                $this->results[] = [
                    'type' => 'blog',
                    'data' => $blog,
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.search-component');
    }
}
