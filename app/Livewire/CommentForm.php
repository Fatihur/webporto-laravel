<?php

namespace App\Livewire;

use App\Models\Comment;
use Livewire\Component;
use Illuminate\Support\Facades\Session;

class CommentForm extends Component
{
    public $blogId;
    public $name = '';
    public $email = '';
    public $content = '';
    public $parentId = null;
    public $replyingToName = null;

    protected $listeners = ['setReplyTo' => 'setReplyTo'];

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'email' => 'required|email|max:255',
        'content' => 'required|min:10|max:2000',
    ];

    protected $messages = [
        'name.required' => 'Name is required.',
        'name.min' => 'Name must be at least 2 characters.',
        'name.max' => 'Name must not exceed 100 characters.',
        'email.required' => 'Email is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.max' => 'Email must not exceed 255 characters.',
        'content.required' => 'Comment is required.',
        'content.min' => 'Comment must be at least 10 characters.',
        'content.max' => 'Comment must not exceed 2000 characters.',
    ];

    public function mount($blogId)
    {
        $this->blogId = $blogId;
    }

    public function setReplyTo($parentId, $name)
    {
        $this->parentId = $parentId;
        $this->replyingToName = $name;
    }

    public function cancelReply()
    {
        $this->parentId = null;
        $this->replyingToName = null;
    }

    public function submit()
    {
        $this->validate();

        $isReply = $this->parentId !== null;

        Comment::create([
            'blog_id' => $this->blogId,
            'parent_id' => $this->parentId,
            'name' => $this->name,
            'email' => $this->email,
            'content' => $this->content,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'approved',
        ]);

        // Clear form
        $this->reset(['name', 'email', 'content', 'parentId', 'replyingToName']);

        session()->flash('message', $isReply ? 'Reply posted successfully.' : 'Comment posted successfully.');

        // Emit event to refresh comments list
        $this->dispatch('commentAdded');
    }

    public function render()
    {
        return view('livewire.comment-form');
    }
}
