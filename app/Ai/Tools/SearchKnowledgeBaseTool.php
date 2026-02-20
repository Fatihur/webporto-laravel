<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\KnowledgeEntry;
use App\Services\EmbeddingService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchKnowledgeBaseTool implements Tool
{
    /**
     * Synonym mappings untuk memperluas pencarian keyword.
     */
    private array $synonyms = [
        'harga' => ['harga', 'biaya', 'tarif', 'rate', 'pricing', 'cost', 'range', 'budget', 'mahal', 'murah', 'estimasi'],
        'biaya' => ['biaya', 'harga', 'tarif', 'rate', 'pricing', 'cost', 'budget'],
        'rate' => ['rate', 'harga', 'tarif', 'biaya', 'range', 'pricing'],
        'budget' => ['budget', 'biaya', 'harga', 'range', 'estimasi', 'pricing'],
        'range' => ['range', 'harga', 'biaya', 'rate', 'tarif', 'estimasi'],
        'project' => ['project', 'proyek', 'projek', 'kerjaan', 'pekerjaan', 'job'],
        'website' => ['website', 'web', 'situs', 'online', 'web development', 'web design'],
        'aplikasi' => ['aplikasi', 'app', 'software', 'program', 'sistem', 'mobile app'],
        'layanan' => ['layanan', 'service', 'services', 'jasa', 'paket'],
        'konsultasi' => ['konsultasi', 'consulting', 'advisor', 'consultant', 'konsultan'],
        'skill' => ['skill', 'keahlian', 'kemampuan', 'expertise', 'bisa', 'menguasai', 'ahli'],
        'tech' => ['tech', 'teknologi', 'stack', 'framework', 'tool', 'platform'],
        'kontak' => ['kontak', 'contact', 'hubungi', 'hubung', 'telepon', 'wa', 'whatsapp', 'email'],
        'hubungi' => ['hubungi', 'kontak', 'contact', 'wa', 'whatsapp', 'email', 'telpon'],
        'available' => ['available', 'tersedia', 'siap', 'open', 'bisa', 'free', 'sedia', 'buka'],
        'booking' => ['booking', 'jadwal', 'schedule', 'reservasi', 'pesan'],
        'proses' => ['proses', 'process', 'alur', 'workflow', 'cara kerja', 'metodologi', 'tahapan'],
        'timeline' => ['timeline', 'waktu', 'durasi', 'lama', 'deadline', 'schedule', 'estimasi waktu'],
        'experience' => ['experience', 'pengalaman', 'kerja', 'karir', 'profesional'],
        'portofolio' => ['portofolio', 'portfolio', 'hasil kerja', 'project', 'karya'],
        'fatih' => ['fatih', 'developer', 'programmer', 'web developer', 'software engineer'],
    ];

    private EmbeddingService $embeddingService;

    public function __construct()
    {
        $this->embeddingService = new EmbeddingService;
    }

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search knowledge base entries to answer user questions about Fatih\'s skills, experience, services, work process, pricing, availability, and general information. Uses both semantic search (meaning-based) and keyword search for best results.';
    }

    /**
     * Execute the tool dengan hybrid search (semantic + keyword).
     */
    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'] ?? '';
        $category = $request['category'] ?? null;
        $limit = $request['limit'] ?? 3;

        // Check if we have embeddings in the database
        $hasEmbeddings = KnowledgeEntry::whereNotNull('embedding')->exists();

        $semanticResults = [];

        // Only try semantic search if embeddings exist
        if ($hasEmbeddings) {
            $semanticResults = $this->embeddingService->findSimilar($query, $category, $limit * 2);
        }

        // Always perform keyword search as primary or fallback
        $keywordResults = $this->keywordSearch($query, $category, $limit * 2);

        // Merge dan deduplicate results
        $mergedResults = $this->mergeResults($semanticResults, $keywordResults, $limit);

        if (empty($mergedResults)) {
            return 'No relevant knowledge entries found.';
        }

        // Increment usage count untuk setiap entry yang ditemukan
        foreach ($mergedResults as $result) {
            $result['entry']->incrementUsage();
        }

        return $this->formatResults($mergedResults);
    }

    /**
     * Keyword search dengan intelligent relevance scoring.
     */
    private function keywordSearch(string $query, ?string $category, int $limit): array
    {
        $searchTerms = $this->expandQuery($query);
        $originalTerms = array_map('strtolower', explode(' ', $query));

        // Get all potential matches first
        $entries = KnowledgeEntry::query()
            ->active()
            ->when($searchTerms, function ($q) use ($searchTerms) {
                $q->where(function ($subQ) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $subQ->orWhere(function ($termQ) use ($term) {
                            $termQ->where('title', 'like', "%{$term}%")
                                ->orWhere('content', 'like', "%{$term}%")
                                ->orWhereJsonContains('tags', $term);
                        });
                    }
                });
            })
            ->when($category, function ($q) use ($category) {
                $q->byCategory($category);
            })
            ->limit($limit * 3) // Get more to rank properly
            ->get();

        return $entries->map(function ($entry) use ($searchTerms, $originalTerms) {
            $score = $this->calculateRelevanceScore($entry, $searchTerms, $originalTerms);

            return [
                'entry' => $entry,
                'score' => $score,
                'source' => 'keyword',
            ];
        })
            ->sortByDesc('score')
            ->take($limit)
            ->values()
            ->toArray();
    }

    /**
     * Calculate relevance score based on match quality.
     */
    private function calculateRelevanceScore(KnowledgeEntry $entry, array $searchTerms, array $originalTerms): float
    {
        $score = 0.0;
        $title = strtolower($entry->title);
        $content = strtolower($entry->content);
        $tags = array_map('strtolower', $entry->tags ?? []);

        // Title match is most valuable
        foreach ($originalTerms as $term) {
            if (strlen($term) < 2) {
                continue;
            }
            if (str_contains($title, $term)) {
                $score += 1.0; // Exact title match
                if ($title === $term) {
                    $score += 0.5; // Perfect match
                }
            }
        }

        // Tag match is highly valuable
        foreach ($searchTerms as $term) {
            if (strlen($term) < 2) {
                continue;
            }
            if (in_array($term, $tags)) {
                $score += 0.8;
            }
        }

        // Content match with frequency weighting
        $contentMatches = 0;
        foreach ($searchTerms as $term) {
            if (strlen($term) < 2) {
                continue;
            }
            $count = substr_count($content, $term);
            if ($count > 0) {
                $contentMatches += min($count, 3); // Cap at 3 to avoid spammy content bias
            }
        }
        $score += min($contentMatches * 0.15, 0.6);

        // Boost for exact phrase match in content
        $queryPhrase = strtolower(implode(' ', $originalTerms));
        if (str_contains($content, $queryPhrase)) {
            $score += 0.5;
        }

        // Category relevance boost
        if (in_array($entry->category, ['skills', 'services'])) {
            $score += 0.1; // Slight boost for important categories
        }

        // Usage-based boost (popular entries get slight preference)
        if ($entry->usage_count > 0) {
            $score += min($entry->usage_count * 0.02, 0.1);
        }

        return min($score, 1.0); // Cap at 1.0
    }

    /**
     * Merge semantic dan keyword results dengan deduplication.
     */
    private function mergeResults(array $semanticResults, array $keywordResults, int $limit): array
    {
        $merged = [];
        $seenIds = [];

        // Pertama, tambahkan semantic results (prioritas lebih tinggi)
        foreach ($semanticResults as $result) {
            $id = $result['entry']->id;
            if (! in_array($id, $seenIds)) {
                $merged[] = [
                    'entry' => $result['entry'],
                    'score' => $result['score'],
                    'source' => 'semantic',
                ];
                $seenIds[] = $id;
            }
        }

        // Kemudian tambahkan keyword results yang belum ada
        foreach ($keywordResults as $result) {
            $id = $result['entry']->id;
            if (! in_array($id, $seenIds)) {
                $merged[] = $result;
                $seenIds[] = $id;
            }
        }

        // Sort by score descending
        usort($merged, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($merged, 0, $limit);
    }

    /**
     * Format results untuk AI consumption.
     */
    private function formatResults(array $results): string
    {
        $output = [];

        foreach ($results as $result) {
            $entry = $result['entry'];
            $output[] = [
                'title' => $entry->title,
                'content' => $entry->content,
                'category' => $entry->category,
                'tags' => $entry->tags ?? [],
                'relevance_score' => round($result['score'], 2),
            ];
        }

        return json_encode($output, JSON_PRETTY_PRINT);
    }

    /**
     * Expand query dengan sinonim.
     */
    private function expandQuery(string $query): array
    {
        $query = strtolower($query);
        $words = explode(' ', $query);
        $expanded = [$query];

        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) < 2) {
                continue;
            }

            foreach ($this->synonyms as $key => $synonyms) {
                if ($word === $key || in_array($word, $synonyms)) {
                    $expanded = array_merge($expanded, $synonyms);
                }
            }
        }

        return array_unique($expanded);
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()->description('Search query from user question to find relevant knowledge entries using semantic search'),
            'category' => $schema->string()->description('Filter by category (e.g., skills, services, pricing, process, general)')->nullable(),
            'limit' => $schema->integer()->min(1)->max(5)->description('Maximum number of knowledge entries to return')->nullable(),
        ];
    }
}
