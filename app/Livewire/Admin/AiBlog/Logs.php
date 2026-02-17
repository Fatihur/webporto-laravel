<?php

namespace App\Livewire\Admin\AiBlog;

use App\Models\AiBlogLog;
use Livewire\Component;
use Livewire\WithPagination;

class Logs extends Component
{
    use WithPagination;

    public string $statusFilter = '';

    public ?int $automationFilter = null;

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    protected $queryString = ['statusFilter', 'automationFilter', 'sortField', 'sortDirection'];

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAutomationFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AiBlogLog::query()->with(['automation', 'blog']);

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        // Automation filter
        if ($this->automationFilter) {
            $query->where('ai_blog_automation_id', $this->automationFilter);
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $logs = $query->paginate(15);

        // Get automations for filter dropdown
        $automations = \App\Models\AiBlogAutomation::orderBy('name')->get();

        return view('livewire.admin.ai-blog.logs', [
            'logs' => $logs,
            'automations' => $automations,
        ])->layout('layouts.admin');
    }
}
