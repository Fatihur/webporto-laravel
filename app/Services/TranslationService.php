<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Stichoza\GoogleTranslate\GoogleTranslate;

class TranslationService
{
    private GoogleTranslate $translator;
    private int $cacheDuration;

    public function __construct()
    {
        $this->translator = new GoogleTranslate();
        $this->cacheDuration = config('translation.dynamic_content.cache_duration', 86400 * 30); // 30 days
    }

    /**
     * Translate text from source locale to target locale
     *
     * @param string $text
     * @param string $targetLocale
     * @param string|null $sourceLocale
     * @return string
     */
    public function translate(string $text, string $targetLocale, ?string $sourceLocale = null): string
    {
        if ($sourceLocale === $targetLocale || empty($text)) {
            return $text;
        }

        $cacheKey = $this->getCacheKey($text, $targetLocale, $sourceLocale);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($text, $targetLocale, $sourceLocale) {
            return $this->performTranslation($text, $targetLocale, $sourceLocale);
        });
    }

    /**
     * Translate array of texts
     *
     * @param array $array
     * @param string $targetLocale
     * @param string|null $sourceLocale
     * @return array
     */
    public function translateArray(array $array, string $targetLocale, ?string $sourceLocale = null): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result[$key] = $this->translateArray($value, $targetLocale, $sourceLocale);
            } elseif (is_string($value)) {
                $result[$key] = $this->translate($value, $targetLocale, $sourceLocale);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Clear translation cache for specific locale
     *
     * @param string $targetLocale
     * @return void
     */
    public function clearCache(string $targetLocale): void
    {
        // Note: This is a simplified implementation. In production,
        // you might want to use cache tags or a more sophisticated approach.
        Cache::flush();
    }

    /**
     * Perform actual translation using Google Translate
     *
     * @param string $text
     * @param string $targetLocale
     * @param string|null $sourceLocale
     * @return string
     */
    private function performTranslation(string $text, string $targetLocale, ?string $sourceLocale): string
    {
        try {
            $this->translator->setTarget($targetLocale);
            $this->translator->setSource($sourceLocale ?? 'auto');

            // Rate limiting: 60ms delay between requests (~16 requests per second)
            usleep(60000);

            return $this->translator->translate($text);
        } catch (\Exception $e) {
            \Log::error('Translation failed: ' . $e->getMessage(), [
                'text' => $text,
                'target' => $targetLocale,
            ]);

            // Return original text on failure
            return $text;
        }
    }

    /**
     * Generate cache key for translation
     *
     * @param string $text
     * @param string $targetLocale
     * @param string|null $sourceLocale
     * @return string
     */
    private function getCacheKey(string $text, string $targetLocale, ?string $sourceLocale): string
    {
        $prefix = config('translation.dynamic_content.table_prefix', 'translations');
        $hash = md5($text . ($sourceLocale ?? 'auto'));
        return "{$prefix}.{$targetLocale}.{$hash}";
    }
}
