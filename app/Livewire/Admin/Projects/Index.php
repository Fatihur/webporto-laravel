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

    // Bulk Actions
    public array $selected = [];

    public bool $selectAll = false;

    public string $bulkAction = '';

    protected $queryString = ['search', 'categoryFilter', 'sortField', 'sortDirection'];

    public function updatedSelectAll($value): void
    {
        $projects = $this->getProjectsForBulkAction();

        if ($value) {
            $this->selected = $projects->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function executeBulkAction(): void
    {
        if (empty($this->selected)) {
            $this->dispatch('notify', type: 'error', message: 'No items selected.');

            return;
        }

        if (empty($this->bulkAction)) {
            $this->dispatch('notify', type: 'error', message: 'Please select an action.');

            return;
        }

        switch ($this->bulkAction) {
            case 'delete':
                $this->bulkDelete();
                break;
        }

        $this->reset('selected', 'selectAll', 'bulkAction');
    }

    private function bulkDelete(): void
    {
        $projects = Project::whereIn('id', $this->selected)->get();

        foreach ($projects as $project) {
            if ($project->thumbnail) {
                Storage::disk('public')->delete($project->thumbnail);
            }
            if ($project->gallery) {
                foreach ($project->gallery as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $project->delete();
        }

        $this->dispatch('notify', type: 'success', message: count($this->selected).' projects deleted successfully.');
    }

    private function getProjectsForBulkAction()
    {
        $query = Project::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        return $query->get();
    }

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
        $this->reset('selected', 'selectAll');
    }

    public function render()
    {
        $query = Project::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
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
