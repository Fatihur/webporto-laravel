<?php

namespace App\Livewire\Admin\Comments;

use App\Models\Comment;
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

    protected $listeners = ['refreshComments' => '$refresh'];

    protected $queryString = ['search'];

    public function updatedSelectAll($value): void
    {
        $comments = $this->getCommentsForBulkAction();

        if ($value) {
            $this->selected = $comments->pluck('id')->toArray();
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
            case 'approve':
                $this->bulkApprove();
                break;
        }

        $this->reset('selected', 'selectAll', 'bulkAction');
    }

    private function bulkDelete(): void
    {
        Comment::whereIn('id', $this->selected)->delete();
        $this->dispatch('notify', type: 'success', message: count($this->selected).' comments deleted successfully.');
    }

    private function bulkApprove(): void
    {
        Comment::whereIn('id', $this->selected)->update(['is_approved' => true]);
        $this->dispatch('notify', type: 'success', message: count($this->selected).' comments approved successfully.');
    }

    private function getCommentsForBulkAction()
    {
        $query = Comment::with(['blog', 'parent']);

        if ($this->search) {
            $searchTerm = '%'.$this->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm);
            });
        }

        return $query->get();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->reset('selected', 'selectAll');
    }

    public function delete($commentId)
    {
        $comment = Comment::find($commentId);
        if ($comment) {
            $comment->delete();
            session()->flash('message', 'Comment deleted successfully.');
        }
    }

    public function render()
    {
        $query = Comment::with(['blog', 'parent']);

        // Search
        if ($this->search) {
            $searchTerm = '%'.$this->search.'%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('content', 'like', $searchTerm);
            });
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.comments.index', [
            'comments' => $comments,
        ])->layout('layouts.admin');
    }
}
