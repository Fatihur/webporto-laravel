<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class AiBlogAutomation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'topic_prompt',
        'content_prompt',
        'category',
        'image_url',
        'frequency',
        'scheduled_at',
        'is_active',
        'max_articles_per_day',
        'auto_publish',
        'last_run_at',
        'next_run_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime:H:i:s',
            'is_active' => 'boolean',
            'auto_publish' => 'boolean',
            'last_run_at' => 'datetime',
            'next_run_at' => 'datetime',
        ];
    }

    /**
     * Get the logs for this automation.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AiBlogLog::class);
    }

    /**
     * Scope for active automations.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for automations due to run.
     */
    public function scopeDueForRun(Builder $query): Builder
    {
        return $query->where(function (Builder $q) {
            $q->whereNull('next_run_at')
                ->orWhere('next_run_at', '<=', now());
        });
    }

    /**
     * Calculate the next run time based on frequency.
     */
    public function calculateNextRun(): ?Carbon
    {
        $now = now();
        $scheduledTime = $this->scheduled_at ? $this->scheduled_at->format('H:i:s') : '09:00:00';

        switch ($this->frequency) {
            case 'daily':
                $nextRun = Carbon::parse($now->format('Y-m-d').' '.$scheduledTime);
                if ($nextRun->isPast()) {
                    $nextRun->addDay();
                }

                return $nextRun;

            case 'weekly':
                $nextRun = Carbon::parse($now->format('Y-m-d').' '.$scheduledTime);
                if ($nextRun->isPast()) {
                    $nextRun->addWeek();
                }

                return $nextRun;

            case 'monthly':
                $nextRun = Carbon::parse($now->format('Y-m-d').' '.$scheduledTime);
                if ($nextRun->isPast()) {
                    $nextRun->addMonth();
                }

                return $nextRun;

            case 'custom':
                // For custom frequency, default to daily
                $nextRun = Carbon::parse($now->format('Y-m-d').' '.$scheduledTime);
                if ($nextRun->isPast()) {
                    $nextRun->addDay();
                }

                return $nextRun;

            default:
                return null;
        }
    }

    /**
     * Check if this automation should run now.
     */
    public function shouldRunNow(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        // Check if we've reached the max articles per day
        $todayCount = $this->logs()
            ->where('status', 'success')
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= $this->max_articles_per_day) {
            return false;
        }

        // Check if it's time to run
        if ($this->next_run_at === null) {
            return true;
        }

        return $this->next_run_at <= now();
    }

    /**
     * Get available categories.
     *
     * @return array<string, string>
     */
    public static function getCategories(): array
    {
        return [
            'design' => 'Design',
            'technology' => 'Technology',
            'tutorial' => 'Tutorial',
            'insights' => 'Insights',
        ];
    }

    /**
     * Get available frequencies.
     *
     * @return array<string, string>
     */
    public static function getFrequencies(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'custom' => 'Custom',
        ];
    }

    /**
     * Update the next run time after execution.
     */
    public function updateNextRun(): void
    {
        $this->last_run_at = now();
        $this->next_run_at = $this->calculateNextRun();
        $this->save();
    }
}
