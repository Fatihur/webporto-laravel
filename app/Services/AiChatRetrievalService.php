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
        $intent = $this->detectIntent($query, $tokens);

        if ($query === '') {
            return ['sources' => []];
        }

        $candidates = [];

        $knowledgeEntries = KnowledgeEntry::query()
            ->active()
            ->when($intent['preferredKnowledgeCategories'] !== [], function ($builder) use ($intent): void {
                $builder->whereIn('category', $intent['preferredKnowledgeCategories']);
            })
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
            $source = [
                'type' => 'knowledge',
                'title' => $entry->title,
                'url' => route('home').'#about',
                'snippet' => Str::limit(strip_tags($entry->content), 180),
            ];

            $candidates[] = [
                'score' => $this->calculateRelevanceScore($query, $tokens, $entry->title, $entry->content, 0.9)
                    + $this->knowledgeIntentBoost((string) $entry->category, $intent),
                'source' => $source,
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
            $source = [
                'type' => 'blog',
                'title' => $blog->title,
                'url' => route('blog.show', $blog->slug),
                'snippet' => Str::limit(strip_tags((string) $blog->excerpt), 180),
            ];

            $candidates[] = [
                'score' => $this->calculateRelevanceScore($query, $tokens, $blog->title, (string) $blog->excerpt, 1.0)
                    + $this->sourceTypeBoost('blog', $intent),
                'source' => $source,
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
            $source = [
                'type' => 'project',
                'title' => $project->title,
                'url' => route('projects.show', $project->slug),
                'snippet' => Str::limit(strip_tags((string) $project->description), 180),
            ];

            $candidates[] = [
                'score' => $this->calculateRelevanceScore($query, $tokens, $project->title, (string) $project->description, 0.95)
                    + $this->sourceTypeBoost('project', $intent),
                'source' => $source,
            ];
        }

        usort($candidates, static fn (array $left, array $right): int => $right['score'] <=> $left['score']);

        $sources = [];
        $seen = [];
        foreach ($candidates as $candidate) {
            $source = $candidate['source'];
            $key = strtolower($source['type'].'|'.$source['title'].'|'.$source['url']);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $sources[] = $source;

            if (count($sources) >= $limit) {
                break;
            }
        }

        return ['sources' => $sources];
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
     * @param  array<int, string>  $tokens
     */
    private function calculateRelevanceScore(string $query, array $tokens, string $title, string $content, float $sourceWeight): float
    {
        $normalizedTitle = strtolower(strip_tags($title));
        $normalizedContent = strtolower(strip_tags($content));
        $normalizedQuery = strtolower($query);

        $score = 0.0;

        if ($normalizedQuery !== '' && str_contains($normalizedTitle, $normalizedQuery)) {
            $score += 4.0;
        }

        if ($normalizedQuery !== '' && str_contains($normalizedContent, $normalizedQuery)) {
            $score += 2.0;
        }

        foreach ($tokens as $token) {
            if (str_contains($normalizedTitle, $token)) {
                $score += 1.5;
            }

            if (str_contains($normalizedContent, $token)) {
                $score += 0.7;
            }
        }

        $score += $sourceWeight;

        return $score;
    }

    /**
     * @param  array<int, string>  $tokens
     * @return array{type: string, preferredKnowledgeCategories: array<int, string>}
     */
    private function detectIntent(string $query, array $tokens): array
    {
        $normalized = strtolower($query.' '.implode(' ', $tokens));

        $pricingKeywords = ['harga', 'biaya', 'rate', 'pricing', 'budget', 'quote', 'quotation', 'tarif', 'cost'];
        $contactKeywords = ['kontak', 'contact', 'hubungi', 'email', 'whatsapp', 'wa'];
        $serviceKeywords = ['jasa', 'layanan', 'service', 'services', 'paket'];

        foreach ($pricingKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return [
                    'type' => 'pricing',
                    'preferredKnowledgeCategories' => ['pricing', 'services', 'general'],
                ];
            }
        }

        foreach ($contactKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return [
                    'type' => 'contact',
                    'preferredKnowledgeCategories' => ['contact', 'general'],
                ];
            }
        }

        foreach ($serviceKeywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return [
                    'type' => 'services',
                    'preferredKnowledgeCategories' => ['services', 'skills', 'general'],
                ];
            }
        }

        return [
            'type' => 'general',
            'preferredKnowledgeCategories' => [],
        ];
    }

    /**
     * @param  array{type: string, preferredKnowledgeCategories: array<int, string>}  $intent
     */
    private function sourceTypeBoost(string $sourceType, array $intent): float
    {
        if ($intent['type'] === 'pricing') {
            if ($sourceType === 'project') {
                return -2.2;
            }

            if ($sourceType === 'blog') {
                return -1.2;
            }
        }

        if ($intent['type'] === 'contact' && $sourceType !== 'knowledge') {
            return -0.7;
        }

        return 0.0;
    }

    /**
     * @param  array{type: string, preferredKnowledgeCategories: array<int, string>}  $intent
     */
    private function knowledgeIntentBoost(string $category, array $intent): float
    {
        if ($intent['type'] === 'pricing' && in_array($category, ['pricing', 'services'], true)) {
            return 2.5;
        }

        if ($intent['type'] === 'contact' && $category === 'contact') {
            return 1.8;
        }

        if ($intent['type'] === 'services' && in_array($category, ['services', 'skills'], true)) {
            return 1.2;
        }

        return 0.0;
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
