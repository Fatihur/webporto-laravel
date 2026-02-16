<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'last_activity_at',
        'is_active',
    ];

    protected $casts = [
        'last_activity_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the chat session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for this chat session.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('sent_at');
    }

    /**
     * Get recent messages (last N messages)
     */
    public function recentMessages(int $limit = 10): HasMany
    {
        return $this->hasMany(ChatMessage::class)
            ->orderBy('sent_at', 'desc')
            ->limit($limit);
    }

    /**
     * Update last activity timestamp
     */
    public function touchLastActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for sessions older than given days
     */
    public function scopeOlderThanDays($query, int $days)
    {
        return $query->where('last_activity_at', '<', now()->subDays($days));
    }
}
