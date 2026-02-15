<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PageView extends Model
{
    protected $fillable = [
        'viewable_type',
        'viewable_id',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'country',
        'city',
    ];

    /**
     * Get the parent viewable model.
     */
    public function viewable(): MorphMany
    {
        return $this->morphTo();
    }

    /**
     * Get view count for a model.
     */
    public static function getCount($model): int
    {
        return static::where('viewable_type', get_class($model))
            ->where('viewable_id', $model->id)
            ->count();
    }

    /**
     * Get unique view count for a model.
     */
    public static function getUniqueCount($model): int
    {
        return static::where('viewable_type', get_class($model))
            ->where('viewable_id', $model->id)
            ->distinct('session_id')
            ->count('session_id');
    }
}
