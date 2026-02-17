<?php

namespace App\Livewire\Admin\AiBlog;

use App\Jobs\GenerateAiBlogArticle;
use App\Models\AiBlogAutomation;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $frequencyFilter = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'statusFilter', 'frequencyFilter', 'sortField', 'sortDirection'];

    public function delete(int $id): void
    {
        $automation = AiBlogAutomation::find($id);

        if ($automation) {
            $automation->delete();
            $this->dispatch('notify', type: 'success', message: 'Automation deleted successfully.');
        }
    }

    public function toggleActive(int $id): void
    {
        $automation = AiBlogAutomation::find($id);

        if ($automation) {
            $automation->is_active = ! $automation->is_active;

            // Recalculate next run when activating
            if ($automation->is_active && ! $automation->next_run_at) {
                $automation->next_run_at = $automation->calculateNextRun();
            }

            $automation->save();

            $this->dispatch('notify', type: 'success', message: $automation->is_active ? 'Automation activated.' : 'Automation deactivated.');
        }
    }

    public function runNow(int $id): void
    {
        $automation = AiBlogAutomation::find($id);

        if ($automation) {
            GenerateAiBlogArticle::dispatch($automation);
            $this->dispatch('notify', type: 'success', message: 'Automation job dispatched. Check logs for progress.');
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
        $query = AiBlogAutomation::query();

        // Search
        if ($this->search) {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        // Status filter
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        // Frequency filter
        if ($this->frequencyFilter) {
            $query->where('frequency', $this->frequencyFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $automations = $query->paginate(10);

        return view('livewire.admin.ai-blog.index', [
            'automations' => $automations,
            'frequencies' => AiBlogAutomation::getFrequencies(),
        ])->layout('layouts.admin');
    }
}
