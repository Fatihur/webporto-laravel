<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheInvalidatable
{
    /**
     * Boot the cache invalidatable trait for a model.
     */
    public static function bootCacheInvalidatable(): void
    {
        static::saved(function ($model) {
            $model->clearModelCache();
        });

        static::deleted(function ($model) {
            $model->clearModelCache();
        });
    }

    /**
     * Clear all cache entries related to this model.
     * Must be implemented by each model class.
     */
    abstract public function clearModelCache(): void;
}
