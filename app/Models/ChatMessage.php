<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'role',
        'content',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the chat session that owns this message.
     */
    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    /**
     * Scope for user messages
     */
    public function scopeUser($query)
    {
        return $query->where('role', 'user');
    }

    /**
     * Scope for assistant messages
     */
    public function scopeAssistant($query)
    {
        return $query->where('role', 'assistant');
    }

    /**
     * Get formatted content for AI (strip HTML if needed)
     */
    public function getFormattedContent(): string
    {
        return strip_tags($this->content);
    }

    /**
     * Get token count from metadata
     */
    public function getTokenCount(): ?int
    {
        return $this->metadata['token_count'] ?? null;
    }
}
