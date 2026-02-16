<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat Analytics</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Insights from AI assistant conversations</p>
        </div>
        <div class="flex items-center space-x-2">
            <select wire:model.live="period" class="border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm">
                <option value="24hours">Last 24 Hours</option>
                <option value="7days">Last 7 Days</option>
                <option value="30days">Last 30 Days</option>
                <option value="all">All Time</option>
            </select>
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                Back to Dashboard
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Sessions</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->stats['totalSessions']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Messages</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->stats['totalMessages']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Avg. Messages/Session</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->stats['avgMessages'] }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Unique Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($this->stats['uniqueUsers']) }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Daily Stats Chart --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Daily Message Activity</h2>
            @if($this->dailyStats->isNotEmpty())
                <div class="space-y-3">
                    @foreach($this->dailyStats as $stat)
                        <div class="flex items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400 w-24">{{ \Carbon\Carbon::parse($stat->date)->format('M d') }}</span>
                            <div class="flex-1 mx-4">
                                <div class="flex h-4 rounded-full overflow-hidden">
                                    @php
                                        $total = max($stat->count, 1);
                                        $userPercent = ($stat->user_count / $total) * 100;
                                        $assistantPercent = ($stat->assistant_count / $total) * 100;
                                    @endphp
                                    <div class="bg-blue-500" style="width: {{ $userPercent }}%"></div>
                                    <div class="bg-green-500" style="width: {{ $assistantPercent }}%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300 w-12 text-right">{{ $stat->count }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center justify-center space-x-6 mt-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">User</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-gray-600 dark:text-gray-400">Assistant</span>
                    </div>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No data available for this period.</p>
            @endif
        </div>

        {{-- Top Questions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Keywords</h2>
            @if(count($this->topQuestions) > 0)
                <div class="space-y-3">
                    @foreach($this->topQuestions as $keyword => $count)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">{{ $keyword }}</span>
                            <div class="flex items-center">
                                <div class="w-24 h-2 bg-gray-200 dark:bg-gray-700 rounded-full mr-3">
                                    <div class="h-full bg-indigo-500 rounded-full" style="width: {{ min(($count / max(array_values($this->topQuestions))) * 100, 100) }}%"></div>
                                </div>
                                <span class="text-sm text-gray-500 dark:text-gray-400 w-8">{{ $count }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No data available yet.</p>
            @endif
        </div>
    </div>

    {{-- Recent Conversations --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Conversations</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Session ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Messages</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Last Activity</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($this->recentConversations as $session)
                        <tr>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ substr($session->session_id, 0, 8) }}...{{ substr($session->session_id, -4) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($session->user)
                                    <span class="text-sm text-gray-900 dark:text-white">{{ $session->user->email }}</span>
                                @else
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Guest</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $session->messages_count }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $session->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' }}">
                                    {{ $session->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $session->last_activity_at?->diffForHumans() ?? 'Never' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button
                                    wire:click="toggleSessionStatus({{ $session->id }})"
                                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 mr-3"
                                >
                                    {{ $session->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                <button
                                    wire:click="deleteSession({{ $session->id }})"
                                    wire:confirm="Are you sure you want to delete this session and all its messages?"
                                    class="text-sm text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
