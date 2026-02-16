<?php

namespace App\Models;

use App\Traits\CacheInvalidatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class Blog extends Model
{
    use HasFactory, CacheInvalidatable;

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
     * Get content attribute - auto decode if JSON encoded
     */
    public function getContentAttribute($value)
    {
        return $this->cleanHtmlValue($value);
    }

    /**
     * Get excerpt attribute - auto decode if JSON encoded
     */
    public function getExcerptAttribute($value)
    {
        return $this->cleanHtmlValue($value);
    }

    /**
     * Set content attribute - ensure clean HTML before saving
     */
    public function setContentAttribute($value)
    {
        // Only clean if value is string and looks encoded
        if (is_string($value) && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = $this->cleanHtmlValue($value);
        }
        $this->attributes['content'] = $value;
    }

    /**
     * Set excerpt attribute - ensure clean HTML before saving
     */
    public function setExcerptAttribute($value)
    {
        // Only clean if value is string and looks encoded
        if (is_string($value) && str_starts_with($value, '"') && str_ends_with($value, '"')) {
            $value = $this->cleanHtmlValue($value);
        }
        $this->attributes['excerpt'] = $value;
    }

    /**
     * Clean HTML value by decoding JSON encoding and fixing escaped characters.
     * Handles multi-level JSON encoding (string wrapped in quotes multiple times).
     */
    private function cleanHtmlValue($value): mixed
    {
        if (!is_string($value) || empty($value)) {
            return $value;
        }

        // Iteratively decode JSON-encoded strings (handles multi-level encoding)
        $maxIterations = 5;
        while ($maxIterations-- > 0 && is_string($value) && strlen($value) > 2) {
            if ($value[0] === '"' && $value[strlen($value) - 1] === '"') {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE && is_string($decoded)) {
                    $value = $decoded;
                    continue;
                }
            }
            break;
        }

        // Fix remaining escaped characters
        $value = str_replace('\\/', '/', $value);
        $value = str_replace('\\"', '"', $value);
        $value = str_replace("\\'", "'", $value);

        return $value;
    }

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
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category' => $this->category,
            'author' => $this->author,
            'slug' => $this->slug,
        ];
    }
}
