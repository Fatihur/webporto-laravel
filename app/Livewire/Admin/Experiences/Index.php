<?php

namespace App\Livewire\Admin\Experiences;

use App\Models\Experience;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    // Bulk Actions
    public array $selected = [];

    public bool $selectAll = false;

    public string $bulkAction = '';

    protected $queryString = ['search'];

    public function updatedSelectAll($value): void
    {
        $experiences = $this->getExperiencesForBulkAction();

        if ($value) {
            $this->selected = $experiences->pluck('id')->toArray();
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
        Experience::whereIn('id', $this->selected)->delete();
        $this->dispatch('notify', type: 'success', message: count($this->selected).' experiences deleted successfully.');
    }

    private function getExperiencesForBulkAction()
    {
        $query = Experience::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('company', 'like', '%'.$this->search.'%');
            });
        }

        return $query->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->reset('selected', 'selectAll');
    }

    public function delete(int $id): void
    {
        $experience = Experience::find($id);

        if ($experience) {
            $experience->delete();
            $this->dispatch('notify', type: 'success', message: 'Experience deleted successfully.');
        }
    }

    public function moveUp(int $id): void
    {
        $experience = Experience::find($id);
        if ($experience && $experience->order > 0) {
            $swapWith = Experience::where('order', $experience->order - 1)->first();
            if ($swapWith) {
                $swapWith->order = $experience->order;
                $swapWith->save();
                $experience->order = $experience->order - 1;
                $experience->save();
            }
        }
    }

    public function moveDown(int $id): void
    {
        $experience = Experience::find($id);
        $maxOrder = Experience::max('order');
        if ($experience && $experience->order < $maxOrder) {
            $swapWith = Experience::where('order', $experience->order + 1)->first();
            if ($swapWith) {
                $swapWith->order = $experience->order;
                $swapWith->save();
                $experience->order = $experience->order + 1;
                $experience->save();
            }
        }
    }

    public function render()
    {
        $query = Experience::query()->ordered();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('company', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        $experiences = $query->paginate(10);

        return view('livewire.admin.experiences.index', [
            'experiences' => $experiences,
        ])->layout('layouts.admin');
    }
}
