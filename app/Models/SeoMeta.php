<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Traits\CacheInvalidatable;

class SeoMeta extends Model
{
    use HasFactory, CacheInvalidatable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'canonical_url',
        'robots_directives',
        'schema_markup',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'robots_directives' => 'array',
            'schema_markup' => 'array',
        ];
    }

    /**
     * Get the parent seo-metable model.
     */
    public function seoMetaable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get robots directives as string
     */
    public function getRobotsAttribute(): string
    {
        if (empty($this->robots_directives)) {
            return 'index, follow';
        }

        $directives = [];
        foreach ($this->robots_directives as $key => $value) {
            if ($value) {
                $directives[] = $key;
            }
        }

        return implode(', ', $directives) ?: 'index, follow';
    }

    /**
     * Get Open Graph data as array
     */
    public function getOpenGraphData(): array
    {
        return [
            'og:title' => $this->og_title ?? $this->meta_title,
            'og:description' => $this->og_description ?? $this->meta_description,
            'og:image' => $this->og_image,
            'og:type' => $this->og_type ?? 'website',
        ];
    }

    /**
     * Get Twitter Card data as array
     */
    public function getTwitterCardData(): array
    {
        return [
            'twitter:card' => $this->twitter_card ?? 'summary_large_image',
            'twitter:title' => $this->twitter_title ?? $this->meta_title,
            'twitter:description' => $this->twitter_description ?? $this->meta_description,
            'twitter:image' => $this->twitter_image ?? $this->og_image,
        ];
    }

    /**
     * Scope to find by model
     */
    public function scopeForModel($query, Model $model)
    {
        return $query->where('seo_metaable_type', get_class($model))
            ->where('seo_metaable_id', $model->id);
    }

    /**
     * Get or create SEO meta for a model
     */
    public static function getForModel(Model $model): ?self
    {
        return static::forModel($model)->first();
    }

    /**
     * Create or update SEO meta for a model
     */
    public static function updateForModel(Model $model, array $data): self
    {
        $seoMeta = static::firstOrNew([
            'seo_metaable_type' => get_class($model),
            'seo_metaable_id' => $model->id,
        ]);

        $seoMeta->fill($data);
        $seoMeta->save();

        return $seoMeta;
    }
}
