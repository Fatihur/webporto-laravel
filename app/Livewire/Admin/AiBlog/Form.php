<?php

namespace App\Livewire\Admin\AiBlog;

use App\Ai\Agents\BlogWriterAgent;
use App\Models\AiBlogAutomation;
use App\Models\Blog;
use App\Services\UnsplashImageService;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    public ?int $automationId = null;

    // Form fields
    public string $name = '';

    public string $topic_prompt = '';

    public string $content_prompt = '';

    public string $category = 'technology';

    public string $frequency = 'daily';

    public string $scheduled_at = '09:00';

    public bool $is_active = true;

    public int $max_articles_per_day = 1;

    public bool $auto_publish = true;

    public ?string $image_url = null;

    // Options
    public array $categories = [];

    public array $frequencies = [];

    // Test prompt properties
    public bool $showTestModal = false;

    public bool $isTesting = false;

    public ?array $testResult = null;

    public ?string $testError = null;

    protected function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'topic_prompt' => 'required|min:10',
            'content_prompt' => 'required|min:10',
            'category' => 'required|in:design,technology,tutorial,insights',
            'frequency' => 'required|in:daily,weekly,monthly,custom',
            'scheduled_at' => 'required|date_format:H:i',
            'is_active' => 'boolean',
            'max_articles_per_day' => 'required|integer|min:1|max:10',
            'auto_publish' => 'boolean',
            'image_url' => 'nullable|url|max:2048',
        ];
    }

    public function mount(?int $id = null): void
    {
        $this->automationId = $id;
        $this->categories = AiBlogAutomation::getCategories();
        $this->frequencies = AiBlogAutomation::getFrequencies();

        if ($id) {
            $automation = AiBlogAutomation::find($id);
            if ($automation) {
                $this->name = $automation->name;
                $this->topic_prompt = $automation->topic_prompt;
                $this->content_prompt = $automation->content_prompt;
                $this->category = $automation->category;
                $this->image_url = $automation->image_url;
                $this->frequency = $automation->frequency;
                $this->scheduled_at = $automation->scheduled_at ? $automation->scheduled_at->format('H:i') : '09:00';
                $this->is_active = $automation->is_active;
                $this->max_articles_per_day = $automation->max_articles_per_day;
                $this->auto_publish = $automation->auto_publish;
            }
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'topic_prompt' => $this->topic_prompt,
            'content_prompt' => $this->content_prompt,
            'category' => $this->category,
            'image_url' => $this->image_url ?: null,
            'frequency' => $this->frequency,
            'scheduled_at' => $this->scheduled_at,
            'is_active' => $this->is_active,
            'max_articles_per_day' => $this->max_articles_per_day,
            'auto_publish' => $this->auto_publish,
        ];

        if ($this->automationId) {
            $automation = AiBlogAutomation::find($this->automationId);
            $automation->update($data);

            // Recalculate next run if schedule changed
            if ($automation->is_active) {
                $automation->next_run_at = $automation->calculateNextRun();
                $automation->save();
            }

            $message = 'Automation updated successfully.';
        } else {
            $automation = AiBlogAutomation::create($data);

            // Calculate initial next run
            if ($automation->is_active) {
                $automation->next_run_at = $automation->calculateNextRun();
                $automation->save();
            }

            $message = 'Automation created successfully.';
        }

        $this->dispatch('notify', type: 'success', message: $message);
        $this->redirectRoute('admin.ai-blog.index', navigate: true);
    }

    public function testPrompt(): void
    {
        // Validate minimal input
        if (empty($this->topic_prompt) || strlen($this->topic_prompt) < 10) {
            $this->dispatch('notify', type: 'error', message: 'Topic prompt must be at least 10 characters.');

            return;
        }

        $this->showTestModal = true;
        $this->isTesting = true;
        $this->testResult = null;
        $this->testError = null;

        try {
            $agent = app(BlogWriterAgent::class);

            $result = $agent->generateArticle(
                topicPrompt: $this->topic_prompt,
                contentPrompt: $this->content_prompt ?: 'Write an engaging article in Indonesian language.',
                category: $this->category
            );

            $this->testResult = $result;
        } catch (\Throwable $e) {
            $this->testError = $e->getMessage();
        } finally {
            $this->isTesting = false;
        }
    }

    public function closeTestModal(): void
    {
        $this->showTestModal = false;
        $this->testResult = null;
        $this->testError = null;
    }

    public function testPublish(): void
    {
        if (! $this->testResult) {
            $this->dispatch('notify', type: 'error', message: 'No test result to publish.');

            return;
        }

        try {
            $result = $this->testResult;

            // Generate unique slug
            $slug = $this->generateUniqueSlug($result['title']);

            // Fetch real image from Unsplash
            $imageData = $this->fetchRealImage($result, $this->category);

            // Create blog post
            $blog = Blog::create([
                'title' => $result['title'],
                'slug' => $slug,
                'excerpt' => $result['excerpt'],
                'content' => $result['content'],
                'category' => $this->category,
                'image' => null, // No local image for AI generated posts
                'image_url' => $imageData['url'],
                'image_source' => $imageData['source'],
                'read_time' => $result['estimated_read_time'] ?? 5,
                'author' => 'AI Writer',
                'published_at' => now(),
                'is_published' => true,
                'meta_title' => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keywords' => $this->generateKeywords($result['title'], $this->category),
            ]);

            $this->closeTestModal();
            $this->dispatch('notify', type: 'success', message: 'Article published successfully!');

            // Optionally redirect to the blog
            $this->dispatch('redirect-to-blog', url: route('blog.show', $blog->slug));
        } catch (\Throwable $e) {
            $this->dispatch('notify', type: 'error', message: 'Failed to publish: '.$e->getMessage());
        }
    }

    /**
     * Fetch real image from Unsplash using AI-generated keywords.
     *
     * @param  array<string, mixed>  $articleData
     * @return array{url: string, source: string}
     */
    private function fetchRealImage(array $articleData, string $category): array
    {
        $imageService = app(UnsplashImageService::class);

        // Use AI-generated keywords if available, otherwise use title
        $searchKeywords = $articleData['image_search_keywords'] ?? $articleData['title'];

        // Try to get image from Unsplash
        $imageData = $imageService->getImageByCategory($category, $searchKeywords);

        if ($imageData) {
            return [
                'url' => $imageData['url'],
                'source' => "Unsplash - Photo by {$imageData['photographer']}",
            ];
        }

        // Fallback to verified default images
        $fallback = $imageService->getFallbackImage($category);

        return [
            'url' => $fallback['url'],
            'source' => "{$fallback['source']} - Photo by {$fallback['photographer']}",
        ];
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (Blog::where('slug', $slug)->exists()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function getCategoryImage(string $category): string
    {
        $images = [
            'design' => 'blogs/design-default.jpg',
            'technology' => 'blogs/technology-default.jpg',
            'tutorial' => 'blogs/tutorial-default.jpg',
            'insights' => 'blogs/insights-default.jpg',
        ];

        return $images[$category] ?? 'blogs/default.jpg';
    }

    private function generateKeywords(string $title, string $category): string
    {
        $categoryKeywords = [
            'design' => 'design, ui, ux, graphic design, creative',
            'technology' => 'technology, tech, software, development, programming',
            'tutorial' => 'tutorial, guide, how-to, learn, education',
            'insights' => 'insights, analysis, trends, industry, thought leadership',
        ];

        $titleWords = implode(', ', array_slice(explode(' ', strtolower($title)), 0, 5));
        $categoryKeyword = $categoryKeywords[$category] ?? $category;

        return $titleWords.', '.$categoryKeyword;
    }

    public function render()
    {
        return view('livewire.admin.ai-blog.form')->layout('layouts.admin');
    }
}
