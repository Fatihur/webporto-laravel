<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-3xl font-black tracking-tight">Analytics Dashboard</h1>

        <!-- Time Period Selector -->
        <div class="flex gap-2">
            <button wire:click="$set('days', 7)" class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ $days === 7 ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                7 Days
            </button>
            <button wire:click="$set('days', 30)" class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ $days === 30 ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                30 Days
            </button>
            <button wire:click="$set('days', 90)" class="px-4 py-2 rounded-full text-sm font-bold transition-all {{ $days === 90 ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}">
                90 Days
            </button>
        </div>
    </div>

    <!-- Skeleton Loading -->
    <div wire:loading.delay class="space-y-6">
        <!-- Stats Skeleton -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @for($i = 0; $i < 4; $i++)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <div class="h-4 bg-zinc-100 dark:bg-zinc-800 rounded w-24 mb-3 animate-pulse"></div>
                    <div class="h-8 bg-zinc-100 dark:bg-zinc-800 rounded w-20 animate-pulse"></div>
                </div>
            @endfor
        </div>

        <!-- Tables Skeleton -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @for($i = 0; $i < 2; $i++)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <div class="h-6 bg-zinc-100 dark:bg-zinc-800 rounded w-32 mb-4 animate-pulse"></div>
                    <div class="space-y-3">
                        @for($j = 0; $j < 5; $j++)
                            <div class="h-4 bg-zinc-100 dark:bg-zinc-800 rounded w-full animate-pulse"></div>
                        @endfor
                    </div>
                </div>
            @endfor
        </div>

        <!-- Chart Skeleton -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="h-6 bg-zinc-100 dark:bg-zinc-800 rounded w-32 mb-4 animate-pulse"></div>
            <div class="h-64 bg-zinc-100 dark:bg-zinc-800 rounded animate-pulse"></div>
        </div>
    </div>

    <!-- Actual Content -->
    <div wire:loading.remove class="space-y-6">
        <!-- Overview Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-mint/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-mint"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    </div>
                    <p class="text-sm font-bold text-zinc-500 uppercase tracking-wider">Total Views</p>
                </div>
                <p class="text-3xl font-black">{{ number_format($demographics['total_views']) }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-violet/10 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-violet"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <p class="text-sm font-bold text-zinc-500 uppercase tracking-wider">Unique Visitors</p>
                </div>
                <p class="text-3xl font-black">{{ number_format($demographics['unique_visitors']) }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-blue-600"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                    </div>
                    <p class="text-sm font-bold text-zinc-500 uppercase tracking-wider">Avg. Daily Views</p>
                </div>
                <p class="text-3xl font-black">{{ number_format(round($demographics['total_views'] / $days)) }}</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 dark:bg-orange-900/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-orange-600"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <p class="text-sm font-bold text-zinc-500 uppercase tracking-wider">Avg. Daily Visitors</p>
                </div>
                <p class="text-3xl font-black">{{ number_format(round($demographics['unique_visitors'] / $days)) }}</p>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Countries -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400"><circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    Top Countries
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <th class="text-left py-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Country</th>
                                <th class="text-right py-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($demographics['countries'] as $country)
                                <tr class="border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">
                                    <td class="py-3 font-medium">{{ $country['country'] ?? 'Unknown' }}</td>
                                    <td class="py-3 text-right text-sm text-zinc-500">{{ number_format($country['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-zinc-400">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cities -->
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                    Top Cities
                </h2>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-zinc-100 dark:border-zinc-800">
                                <th class="text-left py-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">City</th>
                                <th class="text-right py-3 text-xs font-bold text-zinc-400 uppercase tracking-wider">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($demographics['cities'] as $city)
                                <tr class="border-b border-zinc-50 dark:border-zinc-800/50 last:border-0">
                                    <td class="py-3">
                                        <span class="font-medium">{{ $city['city'] ?? 'Unknown' }}</span>
                                        <span class="text-xs text-zinc-400 ml-1">({{ $city['country'] ?? 'Unknown' }})</span>
                                    </td>
                                    <td class="py-3 text-right text-sm text-zinc-500">{{ number_format($city['total']) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-8 text-center text-zinc-400">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Popular Content -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400"><path d="m22 11-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 11"/><path d="M22 5 13.03.3a1.94 1.94 0 0 0-2.06 0L2 5"/></svg>
                Popular Content
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Popular Projects -->
                <div>
                    <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-wider mb-3">Projects</h3>
                    <div class="space-y-2">
                        @forelse ($demographics['popular_content']['projects'] as $project)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-xl">
                                <div>
                                    <p class="font-bold">Project #{{ $project['id'] }}</p>
                                    <p class="text-sm text-zinc-400">{{ number_format($project['total']) }} views</p>
                                </div>
                                <a href="{{ route('admin.projects.edit', $project['id']) }}" class="text-mint hover:text-mint/80 text-sm font-bold">View →</a>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400 py-4">No popular projects yet.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Popular Blogs -->
                <div>
                    <h3 class="text-sm font-bold text-zinc-500 uppercase tracking-wider mb-3">Blog Posts</h3>
                    <div class="space-y-2">
                        @forelse ($demographics['popular_content']['blogs'] as $blog)
                            <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800 rounded-xl">
                                <div>
                                    <p class="font-bold">Blog #{{ $blog['id'] }}</p>
                                    <p class="text-sm text-zinc-400">{{ number_format($blog['total']) }} views</p>
                                </div>
                                <a href="{{ route('admin.blogs.edit', $blog['id']) }}" class="text-mint hover:text-mint/80 text-sm font-bold">View →</a>
                            </div>
                        @empty
                            <p class="text-sm text-zinc-400 py-4">No popular blogs yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Views Chart -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <h2 class="text-lg font-bold mb-4 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400"><path d="M3 3v18h18"/><path d="m19 9-5 5-4-4-3 3"/></svg>
                Daily Views
            </h2>
            <div class="h-64">
                <canvas id="dailyViewsChart"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Daily Views Chart
    const dailyViewsData = @json($demographics['daily_views'] ?? []);
    const labels = dailyViewsData.map(item => item.date);
    const data = dailyViewsData.map(item => item.total);

    new Chart(document.getElementById('dailyViewsChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Views',
                data: data,
                borderColor: '#76D7A4',
                backgroundColor: 'rgba(118, 215, 164, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#76D7A4',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                    },
                }
            }
        }
    });
</script>
@endpush
