<?php

namespace App\Livewire\Admin\Comments;

use App\Models\Comment;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $listeners = ['refreshComments' => '$refresh'];

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
            $searchTerm = '%' . $this->search . '%';
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
