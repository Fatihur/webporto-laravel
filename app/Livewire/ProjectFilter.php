<?php

namespace App\Livewire;

use App\Data\CategoryData;
use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProjectFilter extends Component
{
    #[Url(as: 'category')]
    public ?string $selectedCategory = null;

    public $projects;
    public array $categories = [];
    public string $title = 'Latest Projects';
    public string $description = 'A comprehensive showcase of cross-disciplinary work spanning design, development, and complex system architectures.';

    #[Url(as: 'search')]
    public string $search = '';

    public function mount(?string $category = null): void
    {
        $this->selectedCategory = $category;
        // Cache categories for 24 hours
        $this->categories = Cache::remember('categories.all', 86400, function () {
            return CategoryData::all();
        });
        $this->loadProjects();
    }

    public function filterByCategory(?string $categoryId = null): void
    {
        $this->selectedCategory = $categoryId;
        $this->loadProjects();
    }

    public function updatedSearch(): void
    {
        $this->loadProjects();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->loadProjects();
    }

    private function loadProjects(): void
    {
        // Create cache key based on category and search
        if (!empty($this->search)) {
            // Cache search results for 2 minutes (shorter TTL for dynamic content)
            $cacheKey = 'projects.search.' . md5($this->search . '.' . ($this->selectedCategory ?? 'all'));
            $ttl = 120;
        } else {
            // Cache category results for 30 minutes
            $cacheKey = 'projects.category.' . ($this->selectedCategory ?? 'all');
            $ttl = 1800;
        }

        $this->projects = Cache::remember($cacheKey, $ttl, function () {
            return $this->fetchProjectsFromDatabase();
        });

        $this->updateMetaData();
    }

    private function fetchProjectsFromDatabase()
    {
        $query = Project::query();

        // Filter by category
        if ($this->selectedCategory) {
            $query->byCategory($this->selectedCategory);
        }

        // Filter by search term
        if ($this->search) {
            $searchTerm = '%' . strtolower($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhereJsonContains('tags', $this->search);
            });
        }

        // Order by date
        $query->recent();

        return $query->get();
    }

    private function updateMetaData(): void
    {
        // Update title and description
        if ($this->selectedCategory) {
            $categoryData = CategoryData::find($this->selectedCategory);
            if ($categoryData) {
                $this->title = $categoryData['name'];
                $this->description = $categoryData['description'];
            }
        } else {
            $this->title = 'Latest Projects';
            $this->description = 'A comprehensive showcase of cross-disciplinary work spanning design, development, and complex system architectures.';
        }
    }

    public function render()
    {
        return view('livewire.project-filter')->layout('layouts.app');
    }
}
