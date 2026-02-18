<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiBlogLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_blog_automation_id',
        'blog_id',
        'status',
        'generated_title',
        'content_angle',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the automation that owns this log.
     */
    public function automation(): BelongsTo
    {
        return $this->belongsTo(AiBlogAutomation::class, 'ai_blog_automation_id');
    }

    /**
     * Get the blog post that was generated (if successful).
     */
    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    /**
     * Scope for pending logs.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for processing logs.
     */
    public function scopeProcessing(Builder $query): Builder
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope for successful logs.
     */
    public function scopeSuccessful(Builder $query): Builder
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for failed logs.
     */
    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mark this log as processing.
     */
    public function markAsProcessing(): void
    {
        $this->status = 'processing';
        $this->started_at = now();
        $this->save();
    }

    /**
     * Mark this log as successful.
     */
    public function markAsSuccess(Blog $blog, string $title, ?string $contentAngle = null): void
    {
        $this->status = 'success';
        $this->blog_id = $blog->id;
        $this->generated_title = $title;
        $this->content_angle = $contentAngle;
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Mark this log as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->completed_at = now();
        $this->save();
    }

    /**
     * Get status badge color.
     */
    public function getStatusColor(): string
    {
        return match ($this->status) {
            'success' => 'green',
            'failed' => 'red',
            'processing' => 'yellow',
            default => 'gray',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'success' => 'Success',
            'failed' => 'Failed',
            'processing' => 'Processing',
            default => 'Pending',
        };
    }

    /**
     * Get execution duration in seconds.
     */
    public function getDuration(): ?int
    {
        if (! $this->started_at) {
            return null;
        }

        $endTime = $this->completed_at ?? now();

        return (int) $this->started_at->diffInSeconds($endTime);
    }
}
