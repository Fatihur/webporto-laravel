<?php

namespace App\Livewire\Admin\Blogs;

use App\Models\Blog;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $blogId = null;

    // Form fields
    public string $title = '';
    public string $slug = '';
    public string $excerpt = '';
    public string $content = '';
    public string $category = '';
    public ?string $author = '';
    public ?string $published_at = '';
    public bool $is_published = false;
    public int $read_time = 5;

    // Image upload
    public $image = null;
    public ?string $imagePreview = null;

    // Categories list
    public array $categories = [
        'design' => 'Design',
        'technology' => 'Technology',
        'tutorial' => 'Tutorial',
        'insights' => 'Insights',
    ];

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:255',
            'slug' => 'required|unique:blogs,slug' . ($this->blogId ? ',' . $this->blogId : ''),
            'excerpt' => 'required|min:10|max:500',
            'content' => 'required',
            'category' => 'required|in:design,technology,tutorial,insights',
            'author' => 'nullable|string|max:255',
            'published_at' => 'nullable|date',
            'is_published' => 'boolean',
            'read_time' => 'required|integer|min:1|max:120',
            'image' => $this->blogId ? 'nullable|image|max:2048' : 'nullable|image|max:2048',
        ];
    }

    public function mount(?int $id = null): void
    {
        $this->blogId = $id;

        if ($id) {
            $blog = Blog::find($id);
            if ($blog) {
                $this->title = $blog->title;
                $this->slug = $blog->slug;
                $this->excerpt = $blog->excerpt ?? '';
                $this->content = $blog->content ?? '';
                $this->category = $blog->category;
                $this->author = $blog->author;
                $this->published_at = $blog->published_at?->format('Y-m-d');
                $this->is_published = $blog->is_published;
                $this->read_time = $blog->read_time;
                $this->imagePreview = $blog->image ? Storage::url($blog->image) : null;
            }
        } else {
            $this->published_at = now()->format('Y-m-d');
        }
    }

    public function updatedTitle(): void
    {
        if (!$this->blogId) {
            $this->slug = Str::slug($this->title);
        }
    }

    /**
     * Set content from Summernote - called via Alpine.js
     */
    public function setContent(string $content): void
    {
        \Log::debug('setContent called', [
            'preview' => substr($content, 0, 100),
            'starts_with_quote' => str_starts_with(trim($content), '"'),
            'ends_with_quote' => str_ends_with(trim($content), '"'),
        ]);
        $this->content = $content;
    }

    /**
     * Set excerpt from Summernote - called via Alpine.js
     */
    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function updatedImage(): void
    {
        $this->validateOnly('image');
        // Generate temporary preview URL for new upload
        if ($this->image) {
            $this->imagePreview = $this->image->temporaryUrl();
        }
    }

    public function removeNewImage(): void
    {
        $this->image = null;
        if ($this->blogId) {
            // Restore original preview if editing
            $blog = Blog::find($this->blogId);
            $this->imagePreview = $blog?->image ? Storage::url($blog->image) : null;
        } else {
            $this->imagePreview = null;
        }
    }

    /**
     * Clean HTML content from Summernote - fix escaped characters
     */
    private function cleanHtml(string $html): string
    {
        \Log::debug('cleanHtml input preview', ['preview' => substr($html, 0, 100)]);

        // Fix JSON-encoded strings (from Livewire/Alpine.js transmission)
        // Iteratively decode until no more JSON encoding detected
        $maxIterations = 5;
        while ($maxIterations-- > 0) {
            $trimmed = trim($html);
            if (str_starts_with($trimmed, '"') && str_ends_with($trimmed, '"')) {
                $decoded = json_decode($trimmed, true);
                if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                    $html = $decoded;
                    \Log::debug('cleanHtml decoded JSON layer');
                    continue;
                }
            }
            break;
        }

        // Fix escaped characters
        $html = str_replace('\\/', '/', $html);
        $html = str_replace('\\"', '"', $html);
        $html = str_replace("\\'", "'", $html);
        $html = str_replace('\\\\', '\\', $html);

        \Log::debug('cleanHtml output preview', ['preview' => substr($html, 0, 100)]);

        return $html;
    }

    public function save(): void
    {
        $this->validate();

        \Log::debug('BlogForm save - raw content preview', [
            'content_preview' => substr($this->content, 0, 100),
            'excerpt_preview' => substr($this->excerpt, 0, 100),
            'content_starts_with_quote' => str_starts_with(trim($this->content), '"'),
            'content_ends_with_quote' => str_ends_with(trim($this->content), '"'),
        ]);

        // Clean content from Summernote JSON encoding
        $cleanContent = $this->cleanHtml($this->content);
        $cleanExcerpt = $this->cleanHtml($this->excerpt);

        \Log::debug('BlogForm save - cleaned content preview', [
            'clean_content_preview' => substr($cleanContent, 0, 100),
            'clean_excerpt_preview' => substr($cleanExcerpt, 0, 100),
        ]);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $cleanExcerpt,
            'content' => $cleanContent,
            'category' => $this->category,
            'author' => $this->author,
            'published_at' => $this->is_published && $this->published_at ? $this->published_at : null,
            'is_published' => $this->is_published,
            'read_time' => $this->read_time,
        ];

        // Handle image upload with optimization
        if ($this->image) {
            $imageService = app(ImageOptimizationService::class);

            if ($this->blogId) {
                $oldBlog = Blog::find($this->blogId);
                if ($oldBlog?->image) {
                    $imageService->delete($oldBlog->image);
                }
            }

            // Optimize dan convert ke WebP (max 1200px, quality 85%)
            $data['image'] = $imageService->optimize(
                $this->image,
                'blogs',
                ['max_width' => 1200, 'max_height' => 800, 'quality' => 85]
            );
        }

        if ($this->blogId) {
            Blog::find($this->blogId)->update($data);
            $message = 'Blog post updated successfully.';
        } else {
            Blog::create($data);
            $message = 'Blog post created successfully.';
        }

        $this->dispatch('notify', type: 'success', message: $message);
        $this->redirectRoute('admin.blogs.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.blogs.form')->layout('layouts.admin');
    }
}
