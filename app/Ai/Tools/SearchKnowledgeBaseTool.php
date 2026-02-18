<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use App\Models\KnowledgeEntry;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SearchKnowledgeBaseTool implements Tool
{
    /**
     * Synonym mappings untuk memperluas pencarian
     * Kata kunci -> array sinonim/variasi yang relevan
     */
    private array $synonyms = [
        // Pricing/Budget related
        'harga' => ['harga', 'biaya', 'tarif', 'rate', 'pricing', 'cost', 'range', 'budget', 'mahal', 'murah', 'estimasi'],
        'biaya' => ['biaya', 'harga', 'tarif', 'rate', 'pricing', 'cost', 'budget'],
        'rate' => ['rate', 'harga', 'tarif', 'biaya', 'range', 'pricing'],
        'budget' => ['budget', 'biaya', 'harga', 'range', 'estimasi', 'pricing'],
        'range' => ['range', 'harga', 'biaya', 'rate', 'tarif', 'estimasi'],

        // Project related
        'project' => ['project', 'proyek', 'projek', 'kerjaan', 'pekerjaan', 'job'],
        'website' => ['website', 'web', 'situs', 'online'],
        'aplikasi' => ['aplikasi', 'app', 'software', 'program', 'sistem'],

        // Service related
        'layanan' => ['layanan', 'service', 'services', 'jasa', 'paket'],
        'konsultasi' => ['konsultasi', 'consulting', 'advisor', 'consultant', 'konsultan'],

        // Skills related
        'skill' => ['skill', 'keahlian', 'kemampuan', 'expertise', 'bisa'],
        'tech' => ['tech', 'teknologi', 'stack', 'framework', 'tool'],

        // Contact related
        'kontak' => ['kontak', 'contact', 'hubungi', 'hubung', 'telepon', 'wa', 'whatsapp', 'email'],
        'hubungi' => ['hubungi', 'kontak', 'contact', 'wa', 'whatsapp', 'email', 'telpon'],

        // Availability related
        'available' => ['available', 'tersedia', 'siap', 'open', 'bisa', 'free', 'sedia'],
        'booking' => ['booking', 'jadwal', 'schedule', 'reservasi', 'pesan'],

        // Process related
        'proses' => ['proses', 'process', 'alur', 'workflow', 'cara kerja', 'metodologi'],
        'timeline' => ['timeline', 'waktu', 'durasi', 'lama', 'deadline', 'schedule'],
    ];

    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'Search knowledge base entries to answer user questions about Fatih\'s skills, experience, services, work process, pricing, availability, and general information. This is the primary source for answering general questions.';
    }

    /**
     * Expand query dengan sinonim untuk pencarian lebih luas
     */
    private function expandQuery(string $query): array
    {
        $query = strtolower($query);
        $words = explode(' ', $query);
        $expanded = [$query]; // Original query pertama

        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) < 2) {
                continue;
            }

            // Cek apakah kata ini ada di synonym map
            foreach ($this->synonyms as $key => $synonyms) {
                if ($word === $key || in_array($word, $synonyms)) {
                    // Tambahkan semua sinonim ke expanded list
                    $expanded = array_merge($expanded, $synonyms);
                }
            }
        }

        // Remove duplicates dan return unique terms
        return array_unique($expanded);
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $query = $request['query'] ?? '';
        $category = $request['category'] ?? null;
        $limit = $request['limit'] ?? 3;

        // Expand query dengan sinonim untuk pencarian lebih komprehensif
        $searchTerms = $this->expandQuery($query);

        $entries = KnowledgeEntry::query()
            ->active()
            ->when($searchTerms, function ($q) use ($searchTerms) {
                // Search dengan multiple terms (OR logic)
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
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get();

        if ($entries->isEmpty()) {
            return 'No relevant knowledge entries found.';
        }

        // Increment usage count for each entry found
        foreach ($entries as $entry) {
            $entry->incrementUsage();
        }

        $result = [];
        foreach ($entries as $entry) {
            $result[] = [
                'title' => $entry->title,
                'content' => $entry->content,
                'category' => $entry->category,
                'tags' => $entry->tags ?? [],
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
            'query' => $schema->string()->description('Search keywords from user question to find relevant knowledge entries'),
            'category' => $schema->string()->description('Filter by category (e.g., skills, services, pricing, process, general)')->nullable(),
            'limit' => $schema->integer()->min(1)->max(5)->description('Maximum number of knowledge entries to return')->nullable(),
        ];
    }
}
