<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\Blog;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchBlogsTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search for blog posts/articles by keyword, category, or title. Returns blog titles, excerpts, categories, authors, and publication dates.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'] ?? '';
        $category = $request['category'] ?? null;
        $limit = $request['limit'] ?? 5;

        $blogs = Blog::query()
            ->published()
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('title', 'like', "%{$query}%")
                        ->orWhere('excerpt', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        if ($blogs->isEmpty()) {
            return 'No blog posts found matching the criteria.';
        }

        $result = [];
        foreach ($blogs as $blog) {
            $result[] = [
                'title' => $blog->title,
                'slug' => $blog->slug,
                'excerpt' => strip_tags($blog->excerpt),
                'category' => $blog->category,
                'author' => $blog->author,
                'read_time' => $blog->read_time,
                'published_at' => $blog->published_at?->format('F j, Y'),
            ];
        }

        return json_encode($result, JSON_PRETTY_PRINT);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Search keyword for blog title, excerpt, or content'),
            'category' => $schema->string()->description('Filter by blog category')->nullable(),
            'limit' => $schema->integer()->min(1)->max(10)->description('Maximum number of results to return')->nullable(),
        ];
    }
}
