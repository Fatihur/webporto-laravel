<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $sortField = 'project_date';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'categoryFilter', 'sortField', 'sortDirection'];

    public function delete(int $id): void
    {
        $project = Project::find($id);

        if ($project) {
            // Delete thumbnail if exists
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }

            // Delete gallery images if exists
            if ($project->gallery) {
                foreach ($project->gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $project->delete();
            $this->dispatch('notify', type: 'success', message: 'Project deleted successfully.');
        }
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Project::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $projects = $query->paginate(10);

        // Get unique categories for filter dropdown
        $categories = Project::distinct()->pluck('category');

        return view('livewire.admin.projects.index', [
            'projects' => $projects,
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
