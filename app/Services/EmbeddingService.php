<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\KnowledgeEntry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    /**
     * Generate embedding for text using the configured embedding provider.
     */
    public function generate(string $text): ?array
    {
        try {
            $provider = config('ai.default_for_embeddings', 'openai');
            $config = config("ai.providers.{$provider}");

            // Prepare text (limit to ~8000 chars for efficiency)
            $text = substr($text, 0, 8000);

            // Call embedding API based on provider
            $result = match ($provider) {
                'openai' => $this->callOpenAI($text, $config),
                default => $this->callOpenAI($text, $config),
            };

            // If primary provider fails, try Jina AI as fallback (free tier)
            if ($result === null) {
                Log::info('Primary embedding provider failed, trying Jina AI fallback');
                $result = $this->callJinaAI($text);
            }

            return $result;
        } catch (\Throwable $e) {
            Log::error('Embedding generation failed: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Call OpenAI-compatible embedding API.
     */
    private function callOpenAI(string $text, ?array $config): ?array
    {
        if (! $config || empty($config['key'])) {
            Log::error('OpenAI embedding provider not configured');

            return null;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$config['key'],
            'Content-Type' => 'application/json',
        ])->post(($config['url'] ?? 'https://api.openai.com/v1').'/embeddings', [
            'input' => $text,
            'model' => 'text-embedding-3-small',
        ]);

        if ($response->successful()) {
            return $response->json('data.0.embedding');
        }

        Log::error('OpenAI embedding error: '.$response->body());

        return null;
    }

    /**
     * Call Jina AI embedding API (free tier available).
     */
    private function callJinaAI(string $text): ?array
    {
        try {
            // Jina AI offers free embeddings without API key (rate limited)
            // Or you can get a free API key at https://jina.ai/embeddings/
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://api.jina.ai/v1/embeddings', [
                'model' => 'jina-embeddings-v2-base-en',
                'input' => [$text],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return $data['data'][0]['embedding'] ?? null;
            }

            Log::error('Jina AI embedding error: '.$response->body());

            return null;
        } catch (\Throwable $e) {
            Log::error('Jina AI embedding exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Calculate cosine similarity between two vectors.
     */
    public function cosineSimilarity(array $vecA, array $vecB): float
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        for ($i = 0; $i < count($vecA); $i++) {
            $dotProduct += $vecA[$i] * $vecB[$i];
            $normA += $vecA[$i] ** 2;
            $normB += $vecB[$i] ** 2;
        }

        if ($normA == 0 || $normB == 0) {
            return 0;
        }

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Find similar entries using semantic search.
     *
     * @return array Array of ['entry' => KnowledgeEntry, 'score' => float]
     */
    public function findSimilar(string $query, ?string $category = null, int $limit = 5): array
    {
        $queryEmbedding = $this->generate($query);

        if (! $queryEmbedding) {
            return [];
        }

        $entries = KnowledgeEntry::query()
            ->active()
            ->when($category, fn ($q) => $q->byCategory($category))
            ->whereNotNull('embedding')
            ->get();

        $results = [];

        foreach ($entries as $entry) {
            $score = $this->cosineSimilarity($queryEmbedding, $entry->embedding);
            $results[] = [
                'entry' => $entry,
                'score' => $score,
            ];
        }

        // Sort by score descending
        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        // Filter results with score > 0.5 (relevance threshold)
        $results = array_filter($results, fn ($r) => $r['score'] > 0.5);

        return array_slice($results, 0, $limit);
    }

    /**
     * Generate and save embedding for a knowledge entry.
     */
    public function embedEntry(KnowledgeEntry $entry): bool
    {
        $text = "{$entry->title}\n\n{$entry->content}";
        $embedding = $this->generate($text);

        if ($embedding) {
            $entry->update(['embedding' => $embedding]);

            return true;
        }

        return false;
    }
}
