<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ImageGallery extends Model
{
    protected $fillable = [
        'gallerable_type',
        'gallerable_id',
        'image_path',
        'thumbnail_path',
        'medium_path',
        'large_path',
        'alt_text',
        'title',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the parent gallerable model.
     */
    public function gallerable(): MorphMany
    {
        return $this->morphTo();
    }

    /**
     * Scope to order by order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Scope to filter by gallerable type and ID.
     */
    public function scopeForGallerable($query, $gallerableType, $gallerableId)
    {
        return $query->where('gallerable_type', $gallerableType)
            ->where('gallerable_id', $gallerableId);
    }
}
