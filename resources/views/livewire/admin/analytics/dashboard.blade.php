<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics Dashboard</h1>
</x-slot>

<div class="space-y-6">
    <!-- Time Period Selector -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Time Period</h2>
        <div class="flex gap-2">
            <button wire:click="$set('days', 7)" class="{{ $days === 7 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-4 py-2 rounded-lg font-medium transition duration-200">
                Last 7 days
            </button>
            <button wire:click="$set('days', 30)" class="{{ $days === 30 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-4 py-2 rounded-lg font-medium transition duration-200">
                Last 30 days
            </button>
            <button wire:click="$set('days', 90)" class="{{ $days === 90 ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-4 py-2 rounded-lg font-medium transition duration-200">
                Last 90 days
            </button>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Total Views</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($demographics['total_views']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Unique Visitors</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($demographics['unique_visitors']) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Avg. Daily Views</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format(round($demographics['total_views'] / $days)) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Avg. Daily Visitors</h3>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format(round($demographics['unique_visitors'] / $days)) }}</p>
        </div>
    </div>

    <!-- Countries -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Countries</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Country</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Views</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($demographics['countries'] as $country)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $country['country'] ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($country['total']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Cities -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Cities</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Country</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">City</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Views</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($demographics['cities'] as $city)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $city['country'] ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $city['city'] ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400">
                                {{ number_format($city['total']) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Popular Content -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Popular Content</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Popular Projects -->
            <div>
                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Popular Projects</h3>
                <div class="space-y-3">
                    @forelse ($demographics['popular_content']['projects'] as $project)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Project #{{ $project['id'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($project['total']) }} views</p>
                            </div>
                            <a href="{{ route('admin.projects.edit', $project['id']) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                View
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No popular projects yet.</p>
                    @endforelse
                </div>
            </div>

            <!-- Popular Blogs -->
            <div>
                <h3 class="text-md font-semibold text-gray-900 dark:text-white mb-3">Popular Blogs</h3>
                <div class="space-y-3">
                    @forelse ($demographics['popular_content']['blogs'] as $blog)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Blog #{{ $blog['id'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($blog['total']) }} views</p>
                            </div>
                            <a href="{{ route('admin.blogs.edit') }}/{{ $blog['id'] }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                View
                            </a>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 dark:text-gray-400">No popular blogs yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Views Chart -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Views</h2>
        <div class="h-64">
            <canvas id="dailyViewsChart"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Daily Views Chart
    const dailyViewsData = @json($demographics['daily_views']);
    const labels = dailyViewsData.map(item => item.date);
    const data = dailyViewsData.map(item => item.total);

    new Chart(document.getElementById('dailyViewsChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Daily Views',
                data: data,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1,
                fill: true,
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
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
