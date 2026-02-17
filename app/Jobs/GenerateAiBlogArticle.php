<?php

namespace App\Jobs;

use App\Ai\Agents\BlogWriterAgent;
use App\Models\AiBlogAutomation;
use App\Models\AiBlogLog;
use App\Models\Blog;
use App\Services\UnsplashImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GenerateAiBlogArticle implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 300;

    public function __construct(
        public AiBlogAutomation $automation
    ) {}

    public function handle(BlogWriterAgent $agent): void
    {
        // Create log entry
        $log = AiBlogLog::create([
            'ai_blog_automation_id' => $this->automation->id,
            'status' => 'processing',
            'started_at' => now(),
        ]);

        try {
            DB::beginTransaction();

            // Generate article using AI
            $articleData = $agent->generateArticle(
                topicPrompt: $this->automation->topic_prompt,
                contentPrompt: $this->automation->content_prompt,
                category: $this->automation->category
            );

            // Generate unique slug
            $slug = $this->generateUniqueSlug($articleData['title']);

            // Fetch real image from Unsplash using AI-generated keywords
            $imageData = $this->fetchRealImage($articleData, $this->automation->category);

            // Create blog post
            $blog = Blog::create([
                'title' => $articleData['title'],
                'slug' => $slug,
                'excerpt' => $articleData['excerpt'],
                'content' => $articleData['content'],
                'category' => $this->automation->category,
                'image' => null, // No local image for AI generated posts
                'image_url' => $imageData['url'],
                'image_source' => $imageData['source'],
                'read_time' => $articleData['estimated_read_time'],
                'author' => 'AI Writer',
                'published_at' => $this->automation->auto_publish ? now() : null,
                'is_published' => $this->automation->auto_publish,
                'meta_title' => $articleData['meta_title'],
                'meta_description' => $articleData['meta_description'],
                'meta_keywords' => $this->generateKeywords($articleData['title'], $this->automation->category),
            ]);

            // Update log as successful
            $log->markAsSuccess($blog, $articleData['title']);

            // Update automation run times
            $this->automation->updateNextRun();

            DB::commit();

            Log::info('AI Blog Article generated successfully', [
                'automation_id' => $this->automation->id,
                'blog_id' => $blog->id,
                'title' => $articleData['title'],
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Update log as failed
            $log->markAsFailed($e->getMessage());

            Log::error('AI Blog Article generation failed', [
                'automation_id' => $this->automation->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to allow retry
            throw $e;
        }
    }

    /**
     * Generate a unique slug for the blog post.
     */
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

    /**
     * Get a default image based on category.
     */
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

    /**
     * Generate keywords from title and category.
     */
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
}
