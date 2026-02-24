<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebVitalMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'metric',
        'value',
        'rating',
        'page_group',
        'device_type',
        'connection_type',
        'user_agent_hash',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'float',
            'recorded_at' => 'datetime',
        ];
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('recorded_at', '>=', now()->subDays($days));
    }
}
