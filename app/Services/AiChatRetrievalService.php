<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Blog;
use App\Models\KnowledgeEntry;
use App\Models\Project;
use Illuminate\Support\Str;

class AiChatRetrievalService
{
    /**
     * @return array{sources: array<int, array<string, string>>}
     */
    public function retrieve(string $query, int $limit = 3): array
    {
        $query = trim($query);
        $tokens = $this->tokenizeQuery($query);

        if ($query === '') {
            return ['sources' => []];
        }

        $sources = [];

        $knowledgeEntries = KnowledgeEntry::query()
            ->active()
            ->where(function ($builder) use ($query, $tokens): void {
                $builder
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");

                foreach ($tokens as $token) {
                    $builder
                        ->orWhere('title', 'like', "%{$token}%")
                        ->orWhere('content', 'like', "%{$token}%");
                }
            })
            ->latest('updated_at')
            ->limit($limit)
            ->get(['title', 'content']);

        foreach ($knowledgeEntries as $entry) {
            $sources[] = [
                'type' => 'knowledge',
                'title' => $entry->title,
                'url' => route('home').'#about',
                'snippet' => Str::limit(strip_tags($entry->content), 180),
            ];
        }

        $blogs = Blog::query()
            ->published()
            ->where(function ($builder) use ($query, $tokens): void {
                $builder
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('excerpt', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");

                foreach ($tokens as $token) {
                    $builder
                        ->orWhere('title', 'like', "%{$token}%")
                        ->orWhere('excerpt', 'like', "%{$token}%")
                        ->orWhere('content', 'like', "%{$token}%");
                }
            })
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get(['title', 'slug', 'excerpt']);

        foreach ($blogs as $blog) {
            $sources[] = [
                'type' => 'blog',
                'title' => $blog->title,
                'url' => route('blog.show', $blog->slug),
                'snippet' => Str::limit(strip_tags((string) $blog->excerpt), 180),
            ];
        }

        $projects = Project::query()
            ->where(function ($builder) use ($query, $tokens): void {
                $builder
                    ->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('content', 'like', "%{$query}%");

                foreach ($tokens as $token) {
                    $builder
                        ->orWhere('title', 'like', "%{$token}%")
                        ->orWhere('description', 'like', "%{$token}%")
                        ->orWhere('content', 'like', "%{$token}%");
                }
            })
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get(['title', 'slug', 'description']);

        foreach ($projects as $project) {
            $sources[] = [
                'type' => 'project',
                'title' => $project->title,
                'url' => route('projects.show', $project->slug),
                'snippet' => Str::limit(strip_tags((string) $project->description), 180),
            ];
        }

        return ['sources' => array_slice($sources, 0, $limit)];
    }

    /**
     * @return array<int, string>
     */
    private function tokenizeQuery(string $query): array
    {
        $tokens = preg_split('/\s+/', strtolower($query)) ?: [];

        return array_values(array_filter(array_unique($tokens), fn (string $token): bool => strlen($token) >= 3));
    }

    /**
     * @param  array{sources: array<int, array<string, string>>}  $retrieval
     */
    public function buildPromptContext(array $retrieval): string
    {
        if (empty($retrieval['sources'])) {
            return '';
        }

        $lines = ['=== CONTEXT RUJUKAN ==='];

        foreach ($retrieval['sources'] as $source) {
            $lines[] = "- [{$source['type']}] {$source['title']}";
            $lines[] = "  URL: {$source['url']}";
            $lines[] = "  Ringkasan: {$source['snippet']}";
        }

        $lines[] = 'Gunakan konteks di atas jika relevan, dan hindari membuat informasi yang tidak ada.';

        return implode("\n", $lines);
    }

    /**
     * @param  array{sources: array<int, array<string, string>>}  $retrieval
     */
    public function formatCitationBlock(array $retrieval): string
    {
        if (empty($retrieval['sources'])) {
            return '';
        }

        $lines = ['📎 **Sumber rujukan:**'];

        foreach ($retrieval['sources'] as $source) {
            $lines[] = "• [{$source['title']}]({$source['url']})";
        }

        return implode("\n", $lines);
    }
}
