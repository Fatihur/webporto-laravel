<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;

class CommentList extends Component
{
    public $blogId;
    public $comments;

    protected $listeners = ['commentAdded' => 'refreshComments'];

    public function mount($blogId)
    {
        $this->blogId = $blogId;
        $this->loadComments();
    }

    public function loadComments()
    {
        $this->comments = Comment::where('blog_id', $this->blogId)
            ->whereNull('parent_id')
            ->with(['replies' => function ($query) {
                $query->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function refreshComments()
    {
        $this->loadComments();
    }

    public function render()
    {
        return view('livewire.comment-list');
    }
}
