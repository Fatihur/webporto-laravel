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
        'content_angles',
        'last_used_angle',
        'generation_count',
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
            'content_angles' => 'array',
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
     * Get available content angles.
     *
     * @return array<string, string>
     */
    public static function getContentAngles(): array
    {
        return [
            'tutorial' => 'Tutorial Step-by-Step',
            'tips_tricks' => 'Tips & Tricks',
            'best_practices' => 'Best Practices',
            'common_mistakes' => 'Common Mistakes to Avoid',
            'comparison' => 'Comparison / VS',
            'deep_dive' => 'Deep Dive / Advanced',
            'beginner_guide' => 'Beginner\'s Guide',
            'trends' => 'Trends & Updates',
            'case_study' => 'Case Study / Real Example',
            'opinion' => 'Opinion / Editorial',
            'tool_review' => 'Tool / Library Review',
            'cheatsheet' => 'Cheatsheet / Quick Reference',
        ];
    }

    /**
     * Get the next content angle to use (rotation system).
     */
    public function getNextContentAngle(): string
    {
        $angles = $this->content_angles ?? ['tutorial'];

        if (empty($angles)) {
            return 'tutorial';
        }

        // If only one angle, use that
        if (count($angles) === 1) {
            return $angles[0];
        }

        // Find the next angle in rotation
        $lastAngle = $this->last_used_angle;
        $lastIndex = array_search($lastAngle, $angles);

        if ($lastIndex === false || $lastIndex === count($angles) - 1) {
            // Start from beginning or last was not in list
            return $angles[0];
        }

        return $angles[$lastIndex + 1];
    }

    /**
     * Get target audience based on content angle.
     */
    public function getTargetAudienceForAngle(string $angle): string
    {
        $audienceMap = [
            'tutorial' => 'mixed',
            'tips_tricks' => 'intermediate',
            'best_practices' => 'intermediate_to_advanced',
            'common_mistakes' => 'beginner_to_intermediate',
            'comparison' => 'intermediate',
            'deep_dive' => 'advanced',
            'beginner_guide' => 'beginner',
            'trends' => 'mixed',
            'case_study' => 'intermediate_to_advanced',
            'opinion' => 'mixed',
            'tool_review' => 'mixed',
            'cheatsheet' => 'beginner_to_intermediate',
        ];

        return $audienceMap[$angle] ?? 'mixed';
    }

    /**
     * Mark an angle as used and update generation count.
     */
    public function markAngleAsUsed(string $angle): void
    {
        $this->update([
            'last_used_angle' => $angle,
            'generation_count' => $this->generation_count + 1,
        ]);
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
