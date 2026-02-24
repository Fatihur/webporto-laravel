<div class="space-y-6 sm:space-y-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Performance & Reliability</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Core Web Vitals, queue health, and cache strategy overview.</p>
        </div>
    </div>

    <section class="space-y-4">
        <h2 class="text-lg font-bold">Core Web Vitals ({{ $webVitals['days'] }}d)</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 sm:gap-6">
            @foreach (['LCP', 'INP', 'CLS'] as $metric)
                @php($item = $webVitals['overview'][$metric])
                @php($statusClass = $item['status'] === 'good'
                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                    : ($item['status'] === 'needs-improvement'
                        ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                        : ($item['status'] === 'poor'
                            ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                            : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300')))
                <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                    <div class="flex items-center justify-between mb-3">
                        <p class="font-bold">{{ $metric }}</p>
                        <span class="text-xs font-bold px-2 py-1 rounded-full {{ $statusClass }}">
                            {{ str($item['status'])->replace('-', ' ')->title() }}
                        </span>
                    </div>
                    <p class="text-2xl font-black">
                        {{ $item['p75'] }}{{ $metric !== 'CLS' ? 'ms' : '' }}
                    </p>
                    <p class="text-xs text-zinc-500 mt-1">p75, {{ $item['samples'] }} samples, good rate {{ $item['good_rate'] }}%</p>
                </div>
            @endforeach
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold">Per Page Group Budget Check</h3>
                <span class="text-xs text-zinc-500">{{ $webVitals['sample_count'] }} metrics ingested</span>
            </div>

            @if($webVitals['page_breakdown']->count() > 0)
                <div class="space-y-3">
                    @foreach($webVitals['page_breakdown'] as $group)
                        <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/50">
                            <div class="flex items-center justify-between">
                                <p class="font-semibold text-sm">{{ str($group['page_group'] ?: 'unknown')->replace('_', ' ')->title() }}</p>
                                <p class="text-xs text-zinc-500">{{ $group['samples'] }} samples</p>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($group['metrics'] as $name => $metric)
                                    @php($metricStatusClass = $metric['status'] === 'good'
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'
                                        : ($metric['status'] === 'needs-improvement'
                                            ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                            : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'))
                                    <span class="text-xs font-semibold px-2 py-1 rounded-lg {{ $metricStatusClass }}">
                                        {{ $name }}: {{ $metric['p75'] }}{{ $name !== 'CLS' ? 'ms' : '' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-500">No vitals data yet. Open website pages to start collecting metrics.</p>
            @endif
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-bold">Queue Health</h2>

        @if(count($queueHealth['alerts']) > 0)
            <div class="space-y-3">
                @foreach($queueHealth['alerts'] as $alert)
                    <div class="rounded-xl border p-4 {{ $alert['level'] === 'error' ? 'border-red-200 bg-red-50/80 dark:bg-red-950/20 dark:border-red-900/40' : 'border-amber-200 bg-amber-50/80 dark:bg-amber-950/20 dark:border-amber-900/40' }}">
                        <p class="font-bold text-sm">{{ $alert['title'] }}</p>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 mt-1">{{ $alert['message'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <p class="text-xs text-zinc-500">Pending Jobs</p>
                <p class="text-2xl font-black mt-1">{{ $queueHealth['pending_jobs'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <p class="text-xs text-zinc-500">Failed Jobs</p>
                <p class="text-2xl font-black mt-1">{{ $queueHealth['failed_jobs'] }}</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <p class="text-xs text-zinc-500">Fail Rate (7d)</p>
                <p class="text-2xl font-black mt-1">{{ $queueHealth['fail_rate_7d'] }}%</p>
            </div>
            <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                <p class="text-xs text-zinc-500">P95 Latency (7d)</p>
                <p class="text-2xl font-black mt-1">{{ $queueHealth['p95_latency_seconds'] }}s</p>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <h3 class="font-bold mb-3">Recent AI Job Errors</h3>
            @if($queueHealth['recent_ai_errors']->count() > 0)
                <div class="space-y-2">
                    @foreach($queueHealth['recent_ai_errors'] as $error)
                        <div class="p-3 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg">
                            <p class="text-sm text-zinc-700 dark:text-zinc-200 break-words">{{ $error->error_message }}</p>
                            <p class="text-xs text-zinc-500 mt-1">{{ $error->created_at?->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-zinc-500">No recent AI failures.</p>
            @endif
        </div>
    </section>

    <section class="space-y-4">
        <h2 class="text-lg font-bold">Cache Strategy Snapshot</h2>

        <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
            <p class="text-sm text-zinc-500 mb-3">Current cache versions (used for granular invalidation):</p>
            <div class="flex flex-wrap gap-2">
                @foreach($cacheStrategy['versions'] as $key => $value)
                    <span class="px-3 py-1 text-xs font-semibold rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300">
                        {{ str($key)->replace('_', ' ')->title() }}: v{{ $value }}
                    </span>
                @endforeach
            </div>
        </div>
    </section>
</div>
