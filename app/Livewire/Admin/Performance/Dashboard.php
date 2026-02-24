<?php

namespace App\Livewire\Admin\Performance;

use App\Models\AiBlogLog;
use App\Models\WebVitalMetric;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $data = Cache::remember('admin.performance.dashboard.metrics', now()->addMinutes(1), function (): array {
            return [
                'webVitals' => $this->buildWebVitalsSection(),
                'queue' => $this->buildQueueSection(),
                'cache' => $this->buildCacheSection(),
            ];
        });

        return view('livewire.admin.performance.dashboard', [
            'webVitals' => $data['webVitals'],
            'queueHealth' => $data['queue'],
            'cacheStrategy' => $data['cache'],
        ])->layout('layouts.admin');
    }

    private function buildWebVitalsSection(): array
    {
        $days = 7;
        $budgets = config('performance.web_vitals.budgets', []);

        $rows = WebVitalMetric::query()
            ->recent($days)
            ->select('metric', 'page_group', 'rating', 'value')
            ->get();

        $overview = [];
        foreach (['LCP', 'INP', 'CLS'] as $metric) {
            $metricRows = $rows->where('metric', $metric)->values();
            $values = $metricRows->pluck('value')->map(fn ($v) => (float) $v)->sort()->values();
            $sampleCount = $values->count();

            $overview[$metric] = [
                'samples' => $sampleCount,
                'p75' => $this->percentile($values, 0.75),
                'avg' => $sampleCount > 0 ? round((float) $values->avg(), $metric === 'CLS' ? 3 : 0) : 0,
                'good_rate' => $sampleCount > 0
                    ? (int) round(($metricRows->where('rating', 'good')->count() / $sampleCount) * 100)
                    : 0,
                'status' => $this->budgetStatus($metric, $this->percentile($values, 0.75), $budgets),
            ];
        }

        $pageBreakdown = $rows
            ->groupBy('page_group')
            ->map(function (Collection $groupRows, string $group) use ($budgets): array {
                $metrics = [];

                foreach (['LCP', 'INP', 'CLS'] as $metric) {
                    $metricRows = $groupRows->where('metric', $metric)->values();
                    $values = $metricRows->pluck('value')->map(fn ($v) => (float) $v)->sort()->values();

                    if ($values->isEmpty()) {
                        continue;
                    }

                    $p75 = $this->percentile($values, 0.75);
                    $metrics[$metric] = [
                        'p75' => $p75,
                        'samples' => $values->count(),
                        'status' => $this->budgetStatus($metric, $p75, $budgets),
                    ];
                }

                return [
                    'page_group' => $group,
                    'metrics' => $metrics,
                    'samples' => $groupRows->count(),
                ];
            })
            ->sortByDesc('samples')
            ->take(8)
            ->values();

        return [
            'days' => $days,
            'overview' => $overview,
            'page_breakdown' => $pageBreakdown,
            'budgets' => $budgets,
            'sample_count' => $rows->count(),
        ];
    }

    private function buildQueueSection(): array
    {
        $now = now();
        $lastHour = $now->copy()->subHour();
        $last7d = $now->copy()->subDays(7);

        $pendingJobs = DB::table('jobs')->count();
        $failedJobs = DB::table('failed_jobs')->count();

        $failedLastHour = AiBlogLog::query()
            ->where('status', 'failed')
            ->where('created_at', '>=', $lastHour)
            ->count();

        $logs7d = AiBlogLog::query()
            ->where('created_at', '>=', $last7d)
            ->get(['status', 'started_at', 'completed_at', 'created_at']);

        $totalRuns7d = $logs7d->count();
        $failedRuns7d = $logs7d->where('status', 'failed')->count();
        $successRuns7d = $logs7d->where('status', 'success')->count();
        $processingRuns = AiBlogLog::query()->where('status', 'processing')->count();

        $durations = $logs7d
            ->filter(fn (AiBlogLog $log): bool => $log->started_at !== null && $log->completed_at !== null)
            ->map(fn (AiBlogLog $log): int => $log->getDuration() ?? 0)
            ->filter(fn (int $duration): bool => $duration > 0)
            ->sort()
            ->values();

        $avgLatency = $durations->isNotEmpty() ? (int) round((float) $durations->avg()) : 0;
        $p95Latency = (int) round($this->percentile($durations, 0.95));
        $failRate = $totalRuns7d > 0 ? (int) round(($failedRuns7d / $totalRuns7d) * 100) : 0;

        $alerts = [];
        if ($failedLastHour >= (int) config('performance.alerts.ai_blog_failed_last_hour', 1)) {
            $alerts[] = [
                'level' => 'error',
                'title' => 'AI blog failures detected in the last hour',
                'message' => "{$failedLastHour} failure(s) recorded in the last hour.",
            ];
        }

        if ($failRate >= (int) config('performance.alerts.ai_blog_fail_rate_7d_percent', 25)) {
            $alerts[] = [
                'level' => 'warning',
                'title' => 'AI blog fail rate exceeds budget',
                'message' => "7-day fail rate is {$failRate}% (budget ".config('performance.alerts.ai_blog_fail_rate_7d_percent').'%).',
            ];
        }

        $recentAiErrors = AiBlogLog::query()
            ->where('status', 'failed')
            ->whereNotNull('error_message')
            ->latest('created_at')
            ->limit(5)
            ->get(['id', 'error_message', 'created_at']);

        return [
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
            'processing_ai_runs' => $processingRuns,
            'success_runs_7d' => $successRuns7d,
            'failed_runs_7d' => $failedRuns7d,
            'fail_rate_7d' => $failRate,
            'avg_latency_seconds' => $avgLatency,
            'p95_latency_seconds' => $p95Latency,
            'alerts' => $alerts,
            'recent_ai_errors' => $recentAiErrors,
        ];
    }

    private function buildCacheSection(): array
    {
        return [
            'versions' => [
                'home' => (int) Cache::get('cache.version.home', 1),
                'blog_list' => (int) Cache::get('cache.version.blog.list', 1),
                'blog_detail' => (int) Cache::get('cache.version.blog.detail', 1),
                'blog_related' => (int) Cache::get('cache.version.blog.related', 1),
                'search_blogs' => (int) Cache::get('cache.version.search.blogs', 1),
                'search_projects' => (int) Cache::get('cache.version.search.projects', 1),
            ],
            'budgets' => config('performance.cache', []),
        ];
    }

    private function percentile(Collection $values, float $percentile): float
    {
        if ($values->isEmpty()) {
            return 0;
        }

        $count = $values->count();
        $index = (int) ceil($count * $percentile) - 1;
        $index = max(0, min($count - 1, $index));

        return (float) $values->values()->get($index, 0);
    }

    private function budgetStatus(string $metric, float $value, array $budgets): string
    {
        if ($value <= 0) {
            return 'no-data';
        }

        $budget = $budgets[$metric] ?? null;
        if (! is_array($budget)) {
            return 'unknown';
        }

        if ($value <= (float) ($budget['good'] ?? 0)) {
            return 'good';
        }

        if ($value <= (float) ($budget['needs_improvement'] ?? 0)) {
            return 'needs-improvement';
        }

        return 'poor';
    }
}
