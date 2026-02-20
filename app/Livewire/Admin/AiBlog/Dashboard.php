<?php

namespace App\Livewire\Admin\AiBlog;

use App\Models\AiBlogAutomation;
use App\Models\AiBlogLog;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Statistics
        $stats = [
            'total_automations' => AiBlogAutomation::count(),
            'active_automations' => AiBlogAutomation::where('is_active', true)->count(),
            'total_generated' => AiBlogLog::where('status', 'success')->count(),
            'today_generated' => AiBlogLog::where('status', 'success')
                ->whereDate('created_at', today())
                ->count(),
            'failed_last_7d' => AiBlogLog::where('status', 'failed')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        $logsLast7Days = AiBlogLog::query()
            ->whereNotNull('started_at')
            ->whereNotNull('completed_at')
            ->where('created_at', '>=', now()->subDays(7))
            ->get();

        $durations = $logsLast7Days
            ->map(fn (AiBlogLog $log): int => $log->getDuration() ?? 0)
            ->filter(fn (int $duration): bool => $duration > 0)
            ->sort()
            ->values();

        $medianDuration = 0;
        $durationCount = $durations->count();
        if ($durationCount > 0) {
            $middle = intdiv($durationCount, 2);
            if ($durationCount % 2 === 0) {
                $medianDuration = (int) round(($durations[$middle - 1] + $durations[$middle]) / 2);
            } else {
                $medianDuration = (int) $durations[$middle];
            }
        }

        $metrics = [
            'median_duration_seconds' => $medianDuration,
            'avg_duration_seconds' => $durationCount > 0
                ? (int) round($durations->sum() / $durationCount)
                : 0,
            'retry_count_last_7d' => AiBlogLog::where('status', 'failed')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];

        $topErrors = AiBlogLog::query()
            ->where('status', 'failed')
            ->whereNotNull('error_message')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('error_message, COUNT(*) as total')
            ->groupBy('error_message')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Recent logs
        $recentLogs = AiBlogLog::with(['automation', 'blog'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Upcoming runs
        $upcomingRuns = AiBlogAutomation::where('is_active', true)
            ->whereNotNull('next_run_at')
            ->orderBy('next_run_at')
            ->limit(5)
            ->get();

        // Daily stats for chart (last 7 days)
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            $dailyStats[] = [
                'date' => $dayNames[$date->dayOfWeek].', '.$date->day.' '.$monthNames[$date->month - 1],
                'success' => AiBlogLog::where('status', 'success')
                    ->whereDate('created_at', $date)
                    ->count(),
                'failed' => AiBlogLog::where('status', 'failed')
                    ->whereDate('created_at', $date)
                    ->count(),
            ];
        }

        return view('livewire.admin.ai-blog.dashboard', [
            'stats' => $stats,
            'metrics' => $metrics,
            'topErrors' => $topErrors,
            'recentLogs' => $recentLogs,
            'upcomingRuns' => $upcomingRuns,
            'dailyStats' => $dailyStats,
        ])->layout('layouts.admin');
    }
}
