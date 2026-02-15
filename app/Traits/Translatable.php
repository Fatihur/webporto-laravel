<?php

namespace App\Traits;

use App\Models\Translation;
use App\Services\TranslationService;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\App;

trait Translatable
{
    /**
     * Get all translations for this model.
     *
     * @return MorphMany
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get translation for a specific field and locale.
     *
     * @param string $field
     * @param string|null $locale
     * @return string|null
     */
    public function getTranslation(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();

        // If default locale, return original attribute
        if ($locale === config('app.fallback_locale', 'en')) {
            return $this->getAttributeFromArray($field);
        }

        // Check for cached translation in database
        $translation = $this->translations()
            ->where('locale', $locale)
            ->where('field', $field)
            ->first();

        if ($translation) {
            return $translation->value;
        }

        // Auto-translate if not found
        return $this->autoTranslate($field, $locale);
    }

    /**
     * Auto-translate and cache the result.
     *
     * @param string $field
     * @param string $locale
     * @return string
     */
    protected function autoTranslate(string $field, string $locale): string
    {
        $originalText = $this->getAttributeFromArray($field);

        if (empty($originalText)) {
            return '';
        }

        try {
            $translationService = app(TranslationService::class);
            $translatedText = $translationService->translate(
                $originalText,
                $locale,
                config('app.fallback_locale', 'en')
            );

            // Store in database
            $this->translations()->create([
                'locale' => $locale,
                'field' => $field,
                'value' => $translatedText,
                'is_auto_translated' => true,
            ]);

            return $translatedText;
        } catch (\Exception $e) {
            \Log::error('Auto-translation failed: ' . $e->getMessage(), [
                'model' => get_class($this),
                'id' => $this->getKey(),
                'field' => $field,
                'locale' => $locale,
            ]);

            // Return original text on failure
            return $originalText;
        }
    }

    /**
     * Override getAttribute to check for translations.
     *
     * @param string $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        // Check if this field should be translated
        if (in_array($key, $this->translatableFields ?? [])) {
            return $this->getTranslation($key);
        }

        return parent::getAttribute($key);
    }

    /**
     * Get raw attribute without translation.
     *
     * @param string $field
     * @return mixed
     */
    public function getRawAttribute(string $field): mixed
    {
        return $this->getAttributeFromArray($field);
    }

    /**
     * Set translation for a specific field and locale.
     *
     * @param string $field
     * @param string $locale
     * @param string $value
     * @param bool $isAutoTranslated
     * @return Translation
     */
    public function setTranslation(string $field, string $locale, string $value, bool $isAutoTranslated = false): Translation
    {
        return $this->translations()->updateOrCreate(
            [
                'locale' => $locale,
                'field' => $field,
            ],
            [
                'value' => $value,
                'is_auto_translated' => $isAutoTranslated,
            ]
        );
    }

    /**
     * Clear all translations for this model.
     *
     * @return void
     */
    public function clearTranslations(): void
    {
        $this->translations()->delete();
    }

    /**
     * Clear translations for specific locale.
     *
     * @param string $locale
     * @return void
     */
    public function clearTranslationsForLocale(string $locale): void
    {
        $this->translations()->where('locale', $locale)->delete();
    }

    /**
     * Scope to eager load translations.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $locale
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTranslations($query, ?string $locale = null)
    {
        $locale = $locale ?? App::getLocale();

        return $query->with(['translations' => function ($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    }
}
