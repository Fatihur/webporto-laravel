<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'embedding',
        'metadata',
        'category',
        'tags',
        'is_active',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'embedding' => 'array',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'last_used_at' => 'datetime',
    ];

    /**
     * Scope for active entries
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific category
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Search in title and content
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%")
                ->orWhereJsonContains('tags', $term);
        });
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get formatted content for AI context
     */
    public function toAiContext(): string
    {
        return "[{$this->title}]\n{$this->content}\n";
    }
}
