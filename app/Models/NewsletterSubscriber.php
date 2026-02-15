<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'name',
        'subscribed_at',
        'unsubscribed_at',
        'unsubscribe_token',
        'status',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Subscribe a new email to newsletter.
     */
    public static function subscribe(string $email, ?string $name = null): self
    {
        return static::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'status' => 'active',
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'unsubscribe_token' => Str::random(64),
            ]
        );
    }

    /**
     * Unsubscribe from newsletter.
     */
    public function unsubscribe(): void
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }

    /**
     * Check if subscriber is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if subscriber is unsubscribed.
     */
    public function isUnsubscribed(): bool
    {
        return $this->status === 'unsubscribed';
    }

    /**
     * Scope to only include active subscribers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to only include unsubscribed subscribers.
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', 'unsubscribed');
    }
}
