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

    // Bulk Actions
    public array $selected = [];

    public bool $selectAll = false;

    public string $bulkAction = '';

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
            $blog->is_published = ! $blog->is_published;
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
        $this->reset('selected', 'selectAll');
    }

    public function updatedSelectAll($value): void
    {
        $blogs = $this->getBlogsForBulkAction();

        if ($value) {
            $this->selected = $blogs->pluck('id')->toArray();
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
            case 'publish':
                $this->bulkPublish();
                break;
            case 'unpublish':
                $this->bulkUnpublish();
                break;
        }

        $this->reset('selected', 'selectAll', 'bulkAction');
    }

    private function bulkDelete(): void
    {
        $blogs = Blog::whereIn('id', $this->selected)->get();

        foreach ($blogs as $blog) {
            if ($blog->image) {
                \Storage::disk('public')->delete($blog->image);
            }
            $blog->delete();
        }

        $this->dispatch('notify', type: 'success', message: count($this->selected).' blog posts deleted successfully.');
    }

    private function bulkPublish(): void
    {
        Blog::whereIn('id', $this->selected)->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        $this->dispatch('notify', type: 'success', message: count($this->selected).' blog posts published successfully.');
    }

    private function bulkUnpublish(): void
    {
        Blog::whereIn('id', $this->selected)->update([
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->dispatch('notify', type: 'success', message: count($this->selected).' blog posts unpublished successfully.');
    }

    private function getBlogsForBulkAction()
    {
        $query = Blog::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('excerpt', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        if ($this->statusFilter === 'published') {
            $query->published();
        } elseif ($this->statusFilter === 'drafts') {
            $query->drafts();
        }

        return $query->get();
    }

    public function render()
    {
        $query = Blog::query();

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('excerpt', 'like', '%'.$this->search.'%');
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
