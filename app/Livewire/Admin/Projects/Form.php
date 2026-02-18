<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Ai\Enums\Lab;
use Livewire\Component;
use Livewire\WithFileUploads;

use function Laravel\Ai\agent;

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

    // Translation
    public bool $isTranslating = false;

    protected function rules(): array
    {
        return [
            'title' => 'required|min:3|max:255',
            'slug' => 'required|unique:projects,slug'.($this->projectId ? ','.$this->projectId : ''),
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
                $this->description = $this->cleanHtml($project->description);
                $this->content = $this->cleanHtml($project->content);
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
        if (! $this->projectId) {
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
        if ($value && ! in_array($value, $this->$type)) {
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

        // Clean HTML content
        $cleanContent = $this->cleanHtml($this->content);
        $cleanDescription = $this->cleanHtml($this->description);

        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $cleanDescription,
            'content' => $cleanContent,
            'link' => $this->link,
            'category' => $this->category,
            'project_date' => $this->project_date,
            'tags' => $this->tags,
            'tech_stack' => $this->tech_stack,
            'stats' => array_filter($this->stats, fn ($stat) => ! empty($stat['label']) && ! empty($stat['value'])),
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

    /**
     * Translate project content to English using AI
     */
    public function translateToEnglish(): void
    {
        if (empty($this->title) && empty($this->description) && empty($this->content)) {
            $this->dispatch('notify', type: 'error', message: 'Please fill in at least title, description, or content before translating.');

            return;
        }

        $this->isTranslating = true;

        try {
            // Prepare content to translate
            $contentToTranslate = [];
            if (! empty($this->title)) {
                $contentToTranslate[] = "TITLE: {$this->title}";
            }
            if (! empty($this->description)) {
                $contentToTranslate[] = "DESCRIPTION: {$this->description}";
            }
            if (! empty($this->content)) {
                // Strip HTML tags for cleaner translation
                $plainContent = strip_tags($this->content);
                $contentToTranslate[] = "CONTENT: {$plainContent}";
            }

            $textToTranslate = implode("\n\n", $contentToTranslate);

            $agent = agent(
                instructions: 'You are a professional translator specializing in Indonesian to English translation for tech portfolio content. Translate naturally and professionally, maintaining technical accuracy. Return ONLY the translated text in the same format as input (TITLE:, DESCRIPTION:, CONTENT:).',
            );

            $response = $agent->prompt(
                "Translate the following Indonesian text to English. Maintain professional tech portfolio tone:\n\n{$textToTranslate}",
                provider: Lab::Groq,
            );

            $translatedText = (string) $response;

            // Parse translated content
            if (preg_match('/TITLE:\s*(.+?)(?=\n\n|DESCRIPTION:|CONTENT:|$)/s', $translatedText, $titleMatch)) {
                $this->title = trim($titleMatch[1]);
                // Update slug if it's a new project
                if (! $this->projectId) {
                    $this->slug = Str::slug($this->title);
                }
            }

            if (preg_match('/DESCRIPTION:\s*(.+?)(?=\n\n|TITLE:|CONTENT:|$)/s', $translatedText, $descMatch)) {
                $this->description = trim($descMatch[1]);
            }

            if (preg_match('/CONTENT:\s*(.+?)(?=\n\n|TITLE:|DESCRIPTION:|$)/s', $translatedText, $contentMatch)) {
                $translatedContent = trim($contentMatch[1]);
                // Convert plain text back to simple HTML paragraphs
                $paragraphs = explode("\n\n", $translatedContent);
                $htmlParagraphs = array_map(fn ($p) => '<p>'.trim($p).'</p>', array_filter($paragraphs));
                $this->content = implode("\n", $htmlParagraphs);
            }

            $this->dispatch('notify', type: 'success', message: 'Content translated to English successfully!');
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: 'Translation failed: '.$e->getMessage());
        } finally {
            $this->isTranslating = false;
        }
    }

    public function render()
    {
        return view('livewire.admin.projects.form')->layout('layouts.admin');
    }

    /**
     * Clean HTML content by fixing escaped characters from Summernote/Livewire
     */
    private function cleanHtml(string $html): string
    {
        // Fix escaped forward slashes (\/) -> (/)
        $html = str_replace('\\/', '/', $html);

        // Fix other common escaped characters
        $html = str_replace('\\"', '"', $html);
        $html = str_replace("\\'", "'", $html);
        $html = str_replace('\\\\', '\\', $html);

        // Fix escaped newlines and tabs
        $html = str_replace('\\n', "\n", $html);
        $html = str_replace('\\r', "\r", $html);
        $html = str_replace('\\t', "\t", $html);

        return $html;
    }
}
