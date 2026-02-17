<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UnsplashImageService
{
    private string $accessKey;

    private string $baseUrl = 'https://api.unsplash.com';

    public function __construct()
    {
        $this->accessKey = config('services.unsplash.access_key', '');
    }

    /**
     * Search for a relevant image based on keywords.
     *
     * @param  string  $keywords  Search keywords (e.g., "technology, programming, code")
     * @param  string  $orientation  Photo orientation: 'landscape', 'portrait', 'squarish'
     * @return array|null Returns ['url' => ..., 'source' => ..., 'photographer' => ...] or null
     */
    public function searchImage(string $keywords, string $orientation = 'landscape'): ?array
    {
        if (empty($this->accessKey)) {
            Log::warning('Unsplash API key not configured');

            return null;
        }

        // Cache key based on search terms
        $cacheKey = 'unsplash:'.md5($keywords.$orientation);

        return Cache::remember($cacheKey, now()->addHours(24), function () use ($keywords, $orientation) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Client-ID '.$this->accessKey,
                ])->get("{$this->baseUrl}/search/photos", [
                    'query' => $keywords,
                    'orientation' => $orientation,
                    'per_page' => 10,
                    'order_by' => 'relevant',
                ]);

                if (! $response->successful()) {
                    Log::error('Unsplash API error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    return null;
                }

                $data = $response->json();
                $photos = $data['results'] ?? [];

                if (empty($photos)) {
                    return null;
                }

                // Pick a random photo from top results for variety
                $photo = $photos[array_rand($photos)];

                return [
                    'url' => $photo['urls']['regular'] ?? $photo['urls']['small'],
                    'thumb' => $photo['urls']['small'],
                    'source' => 'Unsplash',
                    'photographer' => $photo['user']['name'] ?? 'Unknown',
                    'photographer_url' => $photo['user']['links']['html'] ?? null,
                    'unsplash_url' => $photo['links']['html'] ?? null,
                ];
            } catch (\Throwable $e) {
                Log::error('Unsplash image fetch failed', [
                    'error' => $e->getMessage(),
                    'keywords' => $keywords,
                ]);

                return null;
            }
        });
    }

    /**
     * Get image by category with predefined search terms.
     */
    public function getImageByCategory(string $category, string $topic = ''): ?array
    {
        $categoryKeywords = [
            'design' => 'web design, graphic design, creative, UI UX, digital art',
            'technology' => 'technology, computer, coding, software, digital',
            'tutorial' => 'education, learning, tutorial, study, knowledge',
            'insights' => 'business, analytics, data, strategy, professional',
        ];

        $baseKeywords = $categoryKeywords[$category] ?? 'technology, business';

        if (! empty($topic)) {
            $keywords = $topic.', '.$baseKeywords;
        } else {
            $keywords = $baseKeywords;
        }

        return $this->searchImage($keywords, 'landscape');
    }

    /**
     * Get a fallback image URL if Unsplash fails.
     */
    public function getFallbackImage(string $category): array
    {
        // These are verified, real Unsplash image URLs as fallback
        $fallbacks = [
            'design' => [
                'url' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=1200&q=80',
                'source' => 'Unsplash',
                'photographer' => 'Balázs Kétyi',
            ],
            'technology' => [
                'url' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=1200&q=80',
                'source' => 'Unsplash',
                'photographer' => 'Compare Fibre',
            ],
            'tutorial' => [
                'url' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=1200&q=80',
                'source' => 'Unsplash',
                'photographer' => 'Unseen Studio',
            ],
            'insights' => [
                'url' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=80',
                'source' => 'Unsplash',
                'photographer' => 'Carlos Muza',
            ],
        ];

        return $fallbacks[$category] ?? $fallbacks['technology'];
    }
}
