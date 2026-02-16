<?php

namespace App\Livewire;

use App\Jobs\TrackPageView;
use App\Models\Blog;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class BlogDetailPage extends Component
{
    public ?Blog $post = null;
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;
        $this->post = Blog::where('slug', $slug)
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->first();

        if (!$this->post) {
            abort(404);
        }

        // Track page view
        TrackPageView::dispatch(
            $this->post,
            Request::ip(),
            Request::userAgent(),
            Request::header('referer'),
            Session::getId()
        );
    }

    public function render()
    {
        // Get related posts (same category, excluding current)
        $relatedPosts = Blog::published()
            ->where('category', $this->post->category)
            ->where('id', '!=', $this->post->id)
            ->limit(2)
            ->get();

        return view('livewire.blog-detail-page', [
            'relatedPosts' => $relatedPosts,
        ])->layout('layouts.app');
    }
}
