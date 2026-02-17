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
            'recentLogs' => $recentLogs,
            'upcomingRuns' => $upcomingRuns,
            'dailyStats' => $dailyStats,
        ])->layout('layouts.admin');
    }
}
