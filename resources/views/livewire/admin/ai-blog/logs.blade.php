<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Generation Logs</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">History of AI-generated blog articles</p>
        </div>
        <a href="{{ route('admin.ai-blog.index') }}" wire:navigate
           class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-xl font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors text-sm sm:text-base">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            <span class="hidden sm:inline">Back to Automations</span>
            <span class="sm:hidden">Back</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Status Filter -->
            <div class="sm:w-40">
                <select wire:model.live="statusFilter"
                        class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Status</option>
                    <option value="success">Success</option>
                    <option value="failed">Failed</option>
                    <option value="processing">Processing</option>
                    <option value="pending">Pending</option>
                </select>
            </div>

            <!-- Automation Filter -->
            <div class="flex-1">
                <select wire:model.live="automationFilter"
                        class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Automations</option>
                    @foreach($automations as $auto)
                        <option value="{{ $auto->id }}">{{ $auto->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if(count($logs) > 0)
        <!-- Skeleton Loading - Desktop -->
        <div wire:loading.delay class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold">Automation</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Generated Title</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Duration</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Created</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-16 mx-auto animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-12 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16 ml-auto animate-pulse"></div>
                        </td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>

        <!-- Skeleton Loading - Mobile -->
        <div wire:loading.delay class="md:hidden space-y-3">
            @for($i = 0; $i < 5; $i++)
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-start justify-between mb-3">
                        <div class="space-y-2 flex-1">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 animate-pulse"></div>
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2 animate-pulse"></div>
                        </div>
                        <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Desktop Table -->
        <div wire:loading.remove class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold">Automation</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Generated Title</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Duration</th>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors"
                        wire:click="sortBy('created_at')">
                        Created
                        @if($sortField === 'created_at')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($logs as $log)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $log->id }}">
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $log->automation?->name ?? 'Deleted' }}</div>
                            @if($log->automation)
                                <div class="text-xs text-zinc-500">{{ $log->automation->category }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($log->generated_title)
                                <div class="font-medium">{{ Str::limit($log->generated_title, 40) }}</div>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-xs font-bold rounded-full
                                @if($log->status === 'success') bg-green-100 text-green-600
                                @elseif($log->status === 'failed') bg-red-100 text-red-600
                                @elseif($log->status === 'processing') bg-yellow-100 text-yellow-600
                                @else bg-zinc-100 text-zinc-500 @endif">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">
                            @if($log->getDuration())
                                {{ $log->getDuration() }}s
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">
                            <div>{{ $log->created_at->format('d M Y, H:i') }}</div>
                            <div class="text-xs">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($log->blog)
                                <a href="{{ route('blog.show', $log->blog->slug) }}" target="_blank"
                                   class="text-sm font-bold text-mint hover:text-mint/80 transition-colors">
                                    View Article
                                </a>
                            @elseif($log->error_message)
                                <span class="text-sm text-red-500" title="{{ $log->error_message }}">
                                    Error
                                </span>
                            @else
                                <span class="text-sm text-zinc-400">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div wire:loading.remove class="md:hidden space-y-3">
            @foreach($logs as $log)
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800" wire:key="mobile-{{ $log->id }}">
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm">{{ $log->automation?->name ?? 'Deleted' }}</div>
                            @if($log->automation)
                                <div class="text-xs text-zinc-500">{{ $log->automation->category }}</div>
                            @endif
                        </div>
                        <span class="px-2 py-0.5 text-xs font-bold rounded-full flex-shrink-0
                            @if($log->status === 'success') bg-green-100 text-green-600
                            @elseif($log->status === 'failed') bg-red-100 text-red-600
                            @elseif($log->status === 'processing') bg-yellow-100 text-yellow-600
                            @else bg-zinc-100 text-zinc-500 @endif">
                            {{ ucfirst($log->status) }}
                        </span>
                    </div>

                    <!-- Title -->
                    <div class="mb-3">
                        @if($log->generated_title)
                            <div class="font-medium text-sm line-clamp-2">{{ $log->generated_title }}</div>
                        @else
                            <span class="text-zinc-400 text-sm">-</span>
                        @endif
                    </div>

                    <!-- Meta -->
                    <div class="flex items-center justify-between text-xs text-zinc-500 mb-3">
                        <span>
                            @if($log->getDuration())
                                {{ $log->getDuration() }}s
                            @else
                                -
                            @endif
                        </span>
                        <span>{{ $log->created_at->diffForHumans() }}</span>
                    </div>

                    <!-- Action -->
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        @if($log->blog)
                            <a href="{{ route('blog.show', $log->blog->slug) }}" target="_blank" class="text-sm font-bold text-mint hover:text-mint/80 transition-colors">
                                View Article →
                            </a>
                        @elseif($log->error_message)
                            <span class="text-sm text-red-500" title="{{ $log->error_message }}">
                                Error
                            </span>
                        @else
                            <span class="text-sm text-zinc-400">-</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-2 sm:px-0">
            {{ $logs->links() }}
        </div>
    @else
        <div class="text-center py-16 sm:py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 mx-2 sm:mx-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-400">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" x2="8" y1="13" y2="13"/>
                    <line x1="16" x2="8" y1="17" y2="17"/>
                    <line x1="10" x2="8" y1="9" y2="9"/>
                </svg>
            </div>
            <p class="text-zinc-500 mb-4 text-sm sm:text-base">No logs found.</p>
            <p class="text-sm text-zinc-400">Logs will appear here when automations run.</p>
        </div>
    @endif
</div>
