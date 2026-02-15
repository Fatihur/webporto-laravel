<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    public ?int $projectId = null;

    // Form fields
    public string $title = '';
    public string $slug = '';
    public string $description = '';
    public string $content = '';
    public ?string $link = '';
    public string $category = '';
    public ?string $project_date = '';
    public array $tags = [];
    public array $tech_stack = [];
    public bool $is_featured = false;

    // Stats as array of objects
    public array $stats = [];

    // Image uploads
    public $thumbnail = null;
    public $thumbnailPreview = null;
    public array $gallery = [];
    public array $galleryPreviews = [];
    public array $existingGallery = [];

    // Categories list
    public array $categories = [
        'graphic-design' => 'Graphic Design',
        'software-dev' => 'Software Development',
        'data-analysis' => 'Data Analysis',
        'networking' => 'Networking',
    ];

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:255',
            'slug' => 'required|unique:projects,slug' . ($this->projectId ? ',' . $this->projectId : ''),
            'description' => 'required|min:10',
            'content' => 'required',
            'link' => 'nullable|url|max:500',
            'category' => 'required|in:graphic-design,software-dev,data-analysis,networking',
            'project_date' => 'required|date',
            'tags' => 'array',
            'tech_stack' => 'array',
            'stats' => 'array',
            'is_featured' => 'boolean',
            'thumbnail' => $this->projectId ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'gallery' => 'array',
            'gallery.*' => 'image|max:2048',
        ];
    }

    public function mount(?int $id = null): void
    {
        $this->projectId = $id;

        if ($id) {
            $project = Project::find($id);
            if ($project) {
                $this->title = $project->title;
                $this->slug = $project->slug;
                $this->description = $project->description;
                $this->content = $project->content;
                $this->link = $project->link ?? '';
                $this->category = $project->category;
                $this->project_date = $project->project_date?->format('Y-m-d');
                $this->tags = $project->tags ?? [];
                $this->tech_stack = $project->tech_stack ?? [];
                $this->stats = $project->stats ?? [['label' => '', 'value' => '']];
                $this->is_featured = $project->is_featured;
                $this->thumbnailPreview = $project->thumbnail ? Storage::url($project->thumbnail) : null;
                $this->existingGallery = $project->gallery ?? [];
            }
        } else {
            $this->stats = [['label' => '', 'value' => '']];
        }
    }

    public function updatedTitle(): void
    {
        if (!$this->projectId) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function updatedThumbnail(): void
    {
        $this->validateOnly('thumbnail');
        // Generate temporary preview URL for new upload
        if ($this->thumbnail) {
            $this->thumbnailPreview = $this->thumbnail->temporaryUrl();
        }
    }

    public function updatedGallery(): void
    {
        $this->validateOnly('gallery.*');
    }

    public function removeNewThumbnail(): void
    {
        $this->thumbnail = null;
        if ($this->projectId) {
            // Restore original preview if editing
            $project = Project::find($this->projectId);
            $this->thumbnailPreview = $project?->thumbnail ? Storage::url($project->thumbnail) : null;
        } else {
            $this->thumbnailPreview = null;
        }
    }

    public function removeNewGalleryImage(int $index): void
    {
        unset($this->gallery[$index]);
        $this->gallery = array_values($this->gallery);
    }

    public function addStat(): void
    {
        $this->stats[] = ['label' => '', 'value' => ''];
    }

    public function removeStat(int $index): void
    {
        unset($this->stats[$index]);
        $this->stats = array_values($this->stats);
    }

    public function addTag(string $value, string $type = 'tags'): void
    {
        $value = trim($value);
        if ($value && !in_array($value, $this->$type)) {
            $this->$type[] = $value;
        }
    }

    public function removeTag(int $index, string $type = 'tags'): void
    {
        unset($this->$type[$index]);
        $this->$type = array_values($this->$type);
    }

    public function removeGalleryImage(int $index): void
    {
        unset($this->existingGallery[$index]);
        $this->existingGallery = array_values($this->existingGallery);
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'link' => $this->link,
            'category' => $this->category,
            'project_date' => $this->project_date,
            'tags' => $this->tags,
            'tech_stack' => $this->tech_stack,
            'stats' => array_filter($this->stats, fn($stat) => !empty($stat['label']) && !empty($stat['value'])),
            'is_featured' => $this->is_featured,
        ];

        // Handle thumbnail upload with optimization
        if ($this->thumbnail) {
            $imageService = app(ImageOptimizationService::class);

            if ($this->projectId) {
                $oldProject = Project::find($this->projectId);
                if ($oldProject?->thumbnail) {
                    $imageService->delete($oldProject->thumbnail);
                }
            }

            // Optimize thumbnail (max 800px, quality 85%)
            $data['thumbnail'] = $imageService->optimize(
                $this->thumbnail,
                'projects/thumbnails',
                ['max_width' => 800, 'max_height' => 600, 'quality' => 85]
            );
        }

        // Handle gallery uploads with optimization
        $imageService = app(ImageOptimizationService::class);
        $galleryPaths = $this->existingGallery;

        foreach ($this->gallery as $image) {
            // Optimize gallery images (max 1600px untuk lightbox, quality 85%)
            $galleryPaths[] = $imageService->optimize(
                $image,
                'projects/gallery',
                ['max_width' => 1600, 'max_height' => 1200, 'quality' => 85]
            );
        }
        $data['gallery'] = $galleryPaths;

        if ($this->projectId) {
            Project::find($this->projectId)->update($data);
            $message = 'Project updated successfully.';
        } else {
            Project::create($data);
            $message = 'Project created successfully.';
        }

        $this->dispatch('notify', type: 'success', message: $message);
        $this->redirectRoute('admin.projects.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.projects.form')->layout('layouts.admin');
    }
}
