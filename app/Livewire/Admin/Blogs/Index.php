<?php

namespace App\Livewire\Admin\Blogs;

use App\Models\Blog;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $statusFilter = '';
    public string $sortField = 'published_at';
    public string $sortDirection = 'desc';

    protected $queryString = ['search', 'categoryFilter', 'statusFilter', 'sortField', 'sortDirection'];

    public function delete(int $id): void
    {
        $blog = Blog::find($id);

        if ($blog) {
            // Delete featured image if exists
            if ($blog->image) {
                \Storage::disk('public')->delete($blog->image);
            }

            $blog->delete();
            $this->dispatch('notify', type: 'success', message: 'Blog post deleted successfully.');
        }
    }

    public function togglePublish(int $id): void
    {
        $blog = Blog::find($id);

        if ($blog) {
            $blog->is_published = !$blog->is_published;
            $blog->published_at = $blog->is_published ? now() : null;
            $blog->save();

            $this->dispatch('notify', type: 'success', message: $blog->is_published ? 'Blog post published.' : 'Blog post unpublished.');
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
        $query = Blog::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%');
            });
        }

        // Category filter
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        // Status filter
        if ($this->statusFilter === 'published') {
            $query->published();
        } elseif ($this->statusFilter === 'drafts') {
            $query->drafts();
        }

        // Sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        $blogs = $query->paginate(10);

        // Get unique categories for filter dropdown
        $categories = Blog::distinct()->pluck('category');

        return view('livewire.admin.blogs.index', [
            'blogs' => $blogs,
            'categories' => $categories,
        ])->layout('layouts.admin');
    }
}
