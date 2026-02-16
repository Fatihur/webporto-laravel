<?php

namespace App\Models;

use App\Events\ContentPublished;
use App\Jobs\GenerateSitemap;
use App\Traits\CacheInvalidatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;

class Project extends Model
{
    use HasFactory, CacheInvalidatable, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'link',
        'category',
        'thumbnail',
        'project_date',
        'tags',
        'tech_stack',
        'stats',
        'gallery',
        'is_featured',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'tags' => 'array',
        'tech_stack' => 'array',
        'stats' => 'array',
        'gallery' => 'array',
        'project_date' => 'date',
        'is_featured' => 'boolean',
    ];

    /**
     * Scope for featured projects
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope ordered by date (newest first)
     */
    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('project_date', 'desc');
    }

    /**
     * Scope by tech stack
     */
    public function scopeByTechStack(Builder $query, array $techStacks): Builder
    {
        return $query->where(function ($q) use ($techStacks) {
            foreach ($techStacks as $tech) {
                $q->orWhereJsonContains('tech_stack', $tech);
            }
        });
    }

    /**
     * Clear all cache entries related to this project.
     */
    public function clearModelCache(): void
    {
        // Clear featured projects cache
        Cache::forget('projects.featured');

        // Clear all category caches
        $categories = ['graphic-design', 'software-dev', 'data-analysis', 'networking'];
        foreach ($categories as $category) {
            Cache::forget('projects.category.' . $category);
        }
        Cache::forget('projects.category.all');

        // Clear specific project cache if exists
        Cache::forget('project.' . $this->slug);

        // Clear related projects cache
        if ($this->category) {
            Cache::forget('projects.related.' . $this->category);
        }
    }

    /**
     * Get the array of data for Scout search.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => strip_tags($this->description),
            'content' => strip_tags($this->content),
            'category' => $this->category,
            'tech_stack' => $this->tech_stack ?? [],
            'tags' => $this->tags ?? [],
            'slug' => $this->slug,
            'project_date' => $this->project_date?->timestamp,
        ];
    }

    /**
     * Get the index settings for Algolia.
     */
    public function scoutIndexSettings(): array
    {
        return [
            'searchableAttributes' => [
                'title',
                'description',
                'content',
                'category',
                'tags',
                'tech_stack',
            ],
            'attributesForFaceting' => [
                'category',
                'tech_stack',
            ],
            'customRanking' => [
                'desc(project_date)',
            ],
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::created(function ($project) {
            // Send newsletter when new project is created
            ContentPublished::dispatch($project, 'project');
        });

        static::saved(function () {
            // Regenerate sitemap when project is saved/updated
            GenerateSitemap::dispatch()->delay(now()->addSeconds(5));
        });

        static::deleted(function () {
            GenerateSitemap::dispatch()->delay(now()->addSeconds(5));
        });
    }
}
