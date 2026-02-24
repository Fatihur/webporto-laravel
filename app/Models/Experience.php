<?php

namespace App\Models;

use App\Traits\CacheInvalidatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Experience extends Model
{
    use CacheInvalidatable, HasFactory;

    protected $fillable = [
        'company',
        'role',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'order',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    /**
     * Scope for current positions
     */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true);
    }

    /**
     * Scope ordered by order field
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('order', 'asc')
            ->orderBy('start_date', 'desc');
    }

    /**
     * Get formatted date range
     */
    public function dateRange(): string
    {
        $start = $this->start_date?->format('M Y') ?? '';
        $end = $this->is_current ? 'Present' : ($this->end_date?->format('M Y') ?? '');

        return "{$start} - {$end}";
    }

    /**
     * Clear all cache entries related to experiences.
     */
    public function clearModelCache(): void
    {
        $currentVersion = (int) Cache::get('cache.version.home', 1);
        Cache::forever('cache.version.home', $currentVersion + 1);

        Cache::forget('experiences.ordered');
        Cache::forget('experiences.current');
    }
}
