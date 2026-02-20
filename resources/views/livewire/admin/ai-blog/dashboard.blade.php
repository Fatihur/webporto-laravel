<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">AI Blog Dashboard</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Overview of AI blog automation</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.ai-blog.logs') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" x2="8" y1="13" y2="13"/>
                    <line x1="16" x2="8" y1="17" y2="17"/>
                    <line x1="10" x2="8" y1="9" y2="9"/>
                </svg>
                View Logs
            </a>
            <a href="{{ route('admin.ai-blog.create') }}" wire:navigate
               class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                <span class="hidden sm:inline">New Automation</span>
                <span class="sm:hidden">New</span>
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6 mb-8">
        <!-- Total Automations -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-violet/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" class="text-violet sm:w-6 sm:h-6">
                        <path d="M12 3v18"/>
                        <path d="M3 12h18"/>
                        <path d="m19 5-7 7-7-7"/>
                        <path d="m19 19-7-7-7 7"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold">{{ $stats['total_automations'] }}</span>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500">Total Automations</p>
        </div>

        <!-- Active Automations -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-green-100 dark:bg-green-900/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" class="text-green-600 sm:w-6 sm:h-6">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="m9 12 2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold">{{ $stats['active_automations'] }}</span>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500">Active Automations</p>
        </div>

        <!-- Total Generated -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-mint/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" class="text-mint sm:w-6 sm:h-6">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Z"/>
                        <path d="M18 14h-8"/>
                        <path d="M15 18h-5"/>
                        <path d="M10 6h8v4h-8V6Z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold">{{ $stats['total_generated'] }}</span>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500">Articles Generated</p>
        </div>

        <!-- Today's Generated -->
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-2 sm:mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" class="text-blue-600 sm:w-6 sm:h-6">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold">{{ $stats['today_generated'] }}</span>
            </div>
            <p class="text-xs sm:text-sm text-zinc-500">Generated Today</p>
        </div>
    </div>

    <!-- Operational Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-6 mb-8">
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <p class="text-xs sm:text-sm text-zinc-500 mb-1">Median Duration (7d)</p>
            <p class="text-2xl sm:text-3xl font-black">{{ $metrics['median_duration_seconds'] }}s</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <p class="text-xs sm:text-sm text-zinc-500 mb-1">Avg Duration (7d)</p>
            <p class="text-2xl sm:text-3xl font-black">{{ $metrics['avg_duration_seconds'] }}s</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <p class="text-xs sm:text-sm text-zinc-500 mb-1">Failed Runs (7d)</p>
            <p class="text-2xl sm:text-3xl font-black">{{ $metrics['retry_count_last_7d'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Daily Stats Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <h2 class="text-base sm:text-lg font-bold mb-4">Last 7 Days Activity</h2>
            <div class="space-y-3">
                @foreach($dailyStats as $day)
                    <div class="flex items-center gap-2 sm:gap-4">
                        <div class="w-16 sm:w-24 text-xs sm:text-sm text-zinc-500 flex-shrink-0">{{ $day['date'] }}</div>
                        <div class="flex-1 h-6 sm:h-8 bg-zinc-100 dark:bg-zinc-800 rounded-lg overflow-hidden flex">
                            @if($day['success'] > 0 || $day['failed'] > 0)
                                @if($day['success'] > 0)
                                    <div class="h-full bg-mint flex items-center justify-center text-xs font-bold text-zinc-950"
                                         style="width: {{ ($day['success'] / max(1, $day['success'] + $day['failed'])) * 100 }}%">
                                        @if($day['success'] > 0) {{ $day['success'] }} @endif
                                    </div>
                                @endif
                                @if($day['failed'] > 0)
                                    <div class="h-full bg-red-400 flex items-center justify-center text-xs font-bold text-white"
                                         style="width: {{ ($day['failed'] / max(1, $day['success'] + $day['failed'])) * 100 }}%">
                                        @if($day['failed'] > 0) {{ $day['failed'] }} @endif
                                    </div>
                                @endif
                            @else
                                <div class="h-full w-full flex items-center justify-center text-xs text-zinc-400">No activity</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-center gap-6 mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded bg-mint"></div>
                    <span class="text-sm text-zinc-500">Success</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded bg-red-400"></div>
                    <span class="text-sm text-zinc-500">Failed</span>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Upcoming Runs -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold mb-4">Upcoming Runs</h2>
                @if(count($upcomingRuns) > 0)
                    <div class="space-y-3">
                        @foreach($upcomingRuns as $run)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                <div>
                                    <p class="font-medium text-sm">{{ $run->name }}</p>
                                    <p class="text-xs text-zinc-500">
                                        {{ $run->next_run_at->format('d M Y, H:i') }}
                                        <span class="text-zinc-400">({{ $run->next_run_at->diffForHumans() }})</span>
                                    </p>
                                </div>
                                <span class="text-xs font-bold px-2 py-1 bg-violet/10 text-violet rounded-full">
                                    {{ ucfirst($run->frequency) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500 text-center py-4">No upcoming runs scheduled</p>
                @endif
            </div>

            <!-- Top Error Messages -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold mb-4">Top Errors (7d)</h2>
                @if(count($topErrors) > 0)
                    <div class="space-y-2">
                        @foreach($topErrors as $error)
                            <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                <p class="text-xs text-zinc-600 dark:text-zinc-300 break-words">{{ $error->error_message }}</p>
                                <p class="text-xs text-zinc-400 mt-1">{{ $error->total }}x</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500 text-center py-4">No recent errors</p>
                @endif
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <h2 class="text-base sm:text-lg font-bold mb-4">Recent Activity</h2>
                @if(count($recentLogs) > 0)
                    <div class="space-y-3">
                        @foreach($recentLogs as $log)
                            <div class="flex items-start gap-3 p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                <div class="w-2 h-2 mt-1.5 rounded-full flex-shrink-0
                                    @if($log->status === 'success') bg-green-500
                                    @elseif($log->status === 'failed') bg-red-500
                                    @else bg-yellow-500 @endif">
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-sm truncate">{{ $log->automation?->name ?? 'Deleted' }}</p>
                                    <p class="text-xs text-zinc-500">{{ $log->created_at->diffForHumans() }}</p>
                                    @if($log->generated_title)
                                        <p class="text-xs text-zinc-400 truncate mt-1">{{ $log->generated_title }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-zinc-500 text-center py-4">No recent activity</p>
                @endif
            </div>
        </div>
    </div>
</div>
