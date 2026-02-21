<?php

namespace App\Livewire\Admin\Projects;

use App\Models\Project;
use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public string $case_study_problem = '';

    public string $case_study_process = '';

    public string $case_study_result = '';

    public ?string $link = '';

    public string $category = '';

    public ?string $project_date = '';

    public array $tags = [];

    public array $tech_stack = [];

    public bool $is_featured = false;

    public ?string $meta_title = null;

    public ?string $meta_description = null;

    public ?string $meta_keywords = null;

    // Stats as array of objects
    public array $stats = [];

    public array $case_study_metrics = [];

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
            'case_study_problem' => 'nullable|string|max:5000',
            'case_study_process' => 'nullable|string|max:5000',
            'case_study_result' => 'nullable|string|max:5000',
            'link' => 'nullable|url|max:500',
            'category' => 'required|in:graphic-design,software-dev,data-analysis,networking',
            'project_date' => 'required|date',
            'tags' => 'array',
            'tech_stack' => 'array',
            'stats' => 'array',
            'case_study_metrics' => 'array',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
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
                $this->case_study_problem = (string) $project->case_study_problem;
                $this->case_study_process = (string) $project->case_study_process;
                $this->case_study_result = (string) $project->case_study_result;
                $this->link = $project->link ?? '';
                $this->category = $project->category;
                $this->project_date = $project->project_date?->format('Y-m-d');
                $this->tags = $project->tags ?? [];
                $this->tech_stack = $project->tech_stack ?? [];
                $this->stats = $project->stats ?? [['label' => '', 'value' => '']];
                $this->case_study_metrics = $project->case_study_metrics ?? [['label' => '', 'value' => '']];
                $this->is_featured = $project->is_featured;
                $this->meta_title = $project->meta_title;
                $this->meta_description = $project->meta_description;
                $this->meta_keywords = $project->meta_keywords;
                $this->thumbnailPreview = $project->thumbnail ? Storage::url($project->thumbnail) : null;
                $this->existingGallery = $project->gallery ?? [];
            }
        } else {
            $this->stats = [['label' => '', 'value' => '']];
            $this->case_study_metrics = [['label' => '', 'value' => '']];
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

    public function addCaseStudyMetric(): void
    {
        $this->case_study_metrics[] = ['label' => '', 'value' => ''];
    }

    public function removeCaseStudyMetric(int $index): void
    {
        unset($this->case_study_metrics[$index]);
        $this->case_study_metrics = array_values($this->case_study_metrics);
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
            'case_study_problem' => $this->cleanHtml($this->case_study_problem),
            'case_study_process' => $this->cleanHtml($this->case_study_process),
            'case_study_result' => $this->cleanHtml($this->case_study_result),
            'link' => $this->link,
            'category' => $this->category,
            'project_date' => $this->project_date,
            'tags' => $this->tags,
            'tech_stack' => $this->tech_stack,
            'stats' => array_filter($this->stats, fn ($stat) => ! empty($stat['label']) && ! empty($stat['value'])),
            'case_study_metrics' => array_filter($this->case_study_metrics, fn ($metric) => ! empty($metric['label']) && ! empty($metric['value'])),
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title ?: $this->title,
            'meta_description' => $this->meta_description ?: Str::limit(strip_tags($cleanDescription), 155),
            'meta_keywords' => $this->meta_keywords ?: implode(', ', array_slice($this->tags, 0, 8)),
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
            $agent = agent(
                instructions: <<<'INSTRUCTIONS'
You are a professional translator specializing in Indonesian to English translation for tech portfolio content.
Translate naturally and professionally, maintaining technical accuracy.

You must return a JSON object with this exact structure:
{
  "title": "translated title here",
  "description": "translated description here",
  "content": "translated content here"
}

If a field is empty or not provided, return empty string for that field.
Preserve HTML formatting in content if present.
INSTRUCTIONS,
            );

            // Build input JSON
            $input = [
                'title' => $this->title,
                'description' => strip_tags($this->description),
                'content' => strip_tags($this->content),
            ];

            $response = $agent->prompt(
                "Translate the following Indonesian text to English. Maintain professional tech portfolio tone:\n\n".json_encode($input, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $translatedText = (string) $response;

            // Extract JSON from response
            $json = $this->extractJsonFromResponse($translatedText);
            $data = json_decode($json, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('Failed to parse translation response: '.json_last_error_msg());
            }

            // Update fields
            if (! empty($data['title'])) {
                $this->title = trim($data['title']);
                if (! $this->projectId) {
                    $this->slug = Str::slug($this->title);
                }
            }

            if (! empty($data['description'])) {
                $this->description = trim($data['description']);
            }

            if (! empty($data['content'])) {
                $translatedContent = trim($data['content']);
                // Convert plain text paragraphs to HTML
                $paragraphs = preg_split('/\n\s*\n/', $translatedContent);
                $htmlParagraphs = array_map(
                    fn ($p) => '<p>'.trim($p).'</p>',
                    array_filter($paragraphs, fn ($p) => ! empty(trim($p)))
                );
                $this->content = implode("\n", $htmlParagraphs);
            }

            $this->dispatch('notify', type: 'success', message: 'Content translated to English successfully!');
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: 'Translation failed: '.$e->getMessage());
        } finally {
            $this->isTranslating = false;
        }
    }

    /**
     * Extract JSON from AI response text
     */
    private function extractJsonFromResponse(string $text): string
    {
        // Try to find JSON between code blocks
        if (preg_match('/```(?:json)?\s*([\s\S]*?)```/', $text, $matches)) {
            return trim($matches[1]);
        }

        // Try to find JSON between curly braces
        if (preg_match('/\{[\s\S]*\}/', $text, $matches)) {
            return $matches[0];
        }

        // Return as-is if it looks like JSON
        $trimmed = trim($text);
        if (str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) {
            return $trimmed;
        }

        throw new \RuntimeException('No valid JSON found in response');
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
