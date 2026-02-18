<?php

namespace App\Livewire\Admin\Knowledge;

use App\Models\KnowledgeEntry;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoryFilter = '';

    public string $statusFilter = '';

    public string $sortField = 'updated_at';

    public string $sortDirection = 'desc';

    public array $selected = [];

    public bool $selectAll = false;

    public string $bulkAction = '';

    protected $queryString = ['search', 'categoryFilter', 'statusFilter', 'sortField', 'sortDirection'];

    public function updatedSelectAll($value): void
    {
        $entries = $this->getEntriesForBulkAction();

        if ($value) {
            $this->selected = $entries->pluck('id')->toArray();
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
            case 'activate':
                $this->bulkActivate();
                break;
            case 'deactivate':
                $this->bulkDeactivate();
                break;
        }

        $this->reset('selected', 'selectAll', 'bulkAction');
    }

    private function bulkDelete(): void
    {
        KnowledgeEntry::whereIn('id', $this->selected)->delete();
        $this->dispatch('notify', type: 'success', message: count($this->selected).' entries deleted successfully.');
    }

    private function bulkActivate(): void
    {
        KnowledgeEntry::whereIn('id', $this->selected)->update(['is_active' => true]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' entries activated successfully.');
    }

    private function bulkDeactivate(): void
    {
        KnowledgeEntry::whereIn('id', $this->selected)->update(['is_active' => false]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' entries deactivated successfully.');
    }

    private function getEntriesForBulkAction()
    {
        $query = KnowledgeEntry::query();

        if ($this->search) {
            $query->search($this->search);
        }

        if ($this->categoryFilter) {
            $query->byCategory($this->categoryFilter);
        }

        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        return $query->get();
    }

    public function delete(int $id): void
    {
        $entry = KnowledgeEntry::find($id);

        if ($entry) {
            $entry->delete();
            $this->dispatch('notify', type: 'success', message: 'Knowledge entry deleted successfully.');
        }
    }

    public function toggleStatus(int $id): void
    {
        $entry = KnowledgeEntry::find($id);

        if ($entry) {
            $entry->is_active = ! $entry->is_active;
            $entry->save();
            $this->dispatch('notify', type: 'success', message: 'Status updated successfully.');
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
        $query = KnowledgeEntry::query();

        // Search
        if ($this->search) {
            $query->search($this->search);
        }

        // Category filter
        if ($this->categoryFilter) {
            $query->byCategory($this->categoryFilter);
        }

        // Status filter
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $entries = $query->paginate(10);

        // Get unique categories for filter dropdown
        $categories = KnowledgeEntry::distinct()->pluck('category')->sort()->values();

        // Get stats
        $stats = [
            'total' => KnowledgeEntry::count(),
            'active' => KnowledgeEntry::active()->count(),
            'inactive' => KnowledgeEntry::where('is_active', false)->count(),
            'mostUsed' => KnowledgeEntry::orderBy('usage_count', 'desc')->first()?->usage_count ?? 0,
        ];

        return view('livewire.admin.knowledge.index', [
            'entries' => $entries,
            'categories' => $categories,
            'stats' => $stats,
        ])->layout('layouts.admin');
    }
}
