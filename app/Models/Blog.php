<?php

namespace App\Models;

use App\Traits\CacheInvalidatable;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Blog extends Model
{
    use HasFactory, CacheInvalidatable, Translatable;

    /**
     * Fields that should be translatable.
     *
     * @var array
     */
    protected array $translatableFields = [
        'title',
        'excerpt',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

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
        'content' => 'array',
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
}
