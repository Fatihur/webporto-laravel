<?php

namespace App\Models;

use App\Jobs\GenerateSitemap;
use App\Traits\CacheInvalidatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;

class Blog extends Model
{
    use HasFactory, CacheInvalidatable, Searchable;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category',
        'image',
        'read_time',
        'author',
        'published_at',
        'is_published',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'published_at' => 'date',
        'is_published' => 'boolean',
    ];

    /**
     * Scope for published blogs
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    /**
     * Scope for drafts
     */
    public function scopeDrafts(Builder $query): Builder
    {
        return $query->where('is_published', false);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Get related blogs (same category, excluding self)
     */
    public function related(int $limit = 2): Builder
    {
        return static::published()
            ->where('category', $this->category)
            ->where('id', '!=', $this->id)
            ->limit($limit);
    }

    /**
     * Get comments for the blog.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get approved comments for the blog.
     */
    public function approvedComments()
    {
        return $this->hasMany(Comment::class)->approved();
    }

    /**
     * Get page views for the blog.
     */
    public function pageViews()
    {
        return $this->morphMany(PageView::class, 'viewable');
    }

    /**
     * Clear all cache entries related to this blog.
     */
    public function clearModelCache(): void
    {
        // Clear all blog pagination caches (first 10 pages)
        for ($i = 1; $i <= 10; $i++) {
            Cache::forget('blog.posts.page.' . $i);
        }

        // Clear categories cache
        Cache::forget('blog.categories');

        // Clear specific post cache
        Cache::forget('blog.post.' . $this->slug);

        // Clear related posts cache
        if ($this->category) {
            Cache::forget('blog.related.' . $this->category);
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
            'excerpt' => strip_tags($this->excerpt),
            'content' => strip_tags($this->content),
            'category' => $this->category,
            'author' => $this->author,
            'slug' => $this->slug,
            'is_published' => $this->is_published,
            'published_at' => $this->published_at?->timestamp,
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
                'excerpt',
                'content',
                'category',
                'author',
            ],
            'attributesForFaceting' => [
                'filterOnly(category)',
                'filterOnly(is_published)',
            ],
            'customRanking' => [
                'desc(published_at)',
            ],
        ];
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::saved(function ($blog) {
            // Regenerate sitemap when blog is published/updated
            if ($blog->is_published) {
                GenerateSitemap::dispatch()->delay(now()->addSeconds(5));
            }
        });

        static::deleted(function () {
            GenerateSitemap::dispatch()->delay(now()->addSeconds(5));
        });
    }
}
