<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">AI Blog Automation</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Manage AI-powered blog article generation</p>
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
                Logs
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

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="text-zinc-400">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Search automations..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="sm:w-40">
                <select wire:model.live="statusFilter"
                        class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <!-- Frequency Filter -->
            <div class="sm:w-40">
                <select wire:model.live="frequencyFilter"
                        class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Frequencies</option>
                    @foreach($frequencies as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @if(count($automations) > 0)
        <!-- Skeleton Loading - Desktop -->
        <div wire:loading.delay class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold">Name</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Category</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Frequency</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Next Run</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-16 mx-auto animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-24 ml-auto animate-pulse"></div>
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
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                    </div>
                    <div class="flex gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <div class="flex-1 h-9 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                        <div class="flex-1 h-9 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                    </div>
                </div>
            @endfor
        </div>

        <!-- Desktop Table -->
        <div wire:loading.remove class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors"
                        wire:click="sortBy('name')">
                        Name
                        @if($sortField === 'name')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Category</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Frequency</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Next Run</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($automations as $automation)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $automation->id }}">
                        <td class="px-6 py-4">
                            <div class="font-bold">{{ $automation->name }}</div>
                            <div class="text-xs text-zinc-500">{{ Str::limit($automation->topic_prompt, 50) }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-violet/10 text-violet text-xs font-bold rounded-full uppercase">
                                {{ $automation->category }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                {{ ucfirst($automation->frequency) }}
                                @if($automation->scheduled_at)
                                    <span class="text-zinc-400">at {{ $automation->scheduled_at->format('H:i') }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">
                            @if($automation->next_run_at && $automation->is_active)
                                <div>{{ $automation->next_run_at->format('d M Y, H:i') }}</div>
                                <div class="text-xs">{{ $automation->next_run_at->diffForHumans() }}</div>
                            @elseif($automation->is_active)
                                <span class="text-yellow-500">Pending schedule</span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="toggleActive({{ $automation->id }})"
                                    class="px-3 py-1 text-xs font-bold rounded-full transition-colors {{ $automation->is_active ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                                {{ $automation->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <button wire:click="runNow({{ $automation->id }})"
                                        class="text-sm font-bold text-mint hover:text-mint/80 transition-colors"
                                        title="Run Now">
                                    Run
                                </button>
                                <a href="{{ route('admin.ai-blog.edit', $automation->id) }}" wire:navigate
                                   class="text-sm font-bold hover:text-mint transition-colors">Edit</a>
                                <button wire:click="delete({{ $automation->id }})"
                                        wire:confirm="Are you sure you want to delete this automation?"
                                        class="text-sm font-bold text-red-500 hover:text-red-600 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div wire:loading.remove class="md:hidden space-y-3">
            @foreach($automations as $automation)
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800" wire:key="mobile-{{ $automation->id }}">
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-sm line-clamp-1">{{ $automation->name }}</h3>
                            <p class="text-xs text-zinc-500 line-clamp-1">{{ Str::limit($automation->topic_prompt, 40) }}</p>
                        </div>
                        <span class="px-2 py-0.5 bg-violet/10 text-violet text-xs font-bold rounded-full uppercase flex-shrink-0">
                            {{ $automation->category }}
                        </span>
                    </div>

                    <!-- Meta Info -->
                    <div class="space-y-1 mb-3">
                        <div class="flex items-center gap-2 text-sm text-zinc-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <polyline points="12 6 12 12 16 14"/>
                            </svg>
                            {{ ucfirst($automation->frequency) }}
                            @if($automation->scheduled_at)
                                <span class="text-zinc-400">at {{ $automation->scheduled_at->format('H:i') }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-zinc-500">
                            @if($automation->next_run_at && $automation->is_active)
                                <span class="text-mint">{{ $automation->next_run_at->diffForHumans() }}</span>
                            @elseif($automation->is_active)
                                <span class="text-yellow-500">Pending schedule</span>
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        </div>
                    </div>

                    <!-- Status & Actions -->
                    <div class="flex items-center gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <button wire:click="toggleActive({{ $automation->id }})" class="px-3 py-1.5 text-xs font-bold rounded-full transition-colors {{ $automation->is_active ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $automation->is_active ? 'Active' : 'Inactive' }}
                        </button>
                        <button wire:click="runNow({{ $automation->id }})" class="flex-1 py-2 text-xs font-bold bg-mint/10 text-mint rounded-lg hover:bg-mint/20 transition-colors">
                            Run
                        </button>
                        <a href="{{ route('admin.ai-blog.edit', $automation->id) }}" wire:navigate class="flex-1 py-2 text-xs font-bold bg-zinc-100 dark:bg-zinc-800 rounded-lg text-center hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                            Edit
                        </a>
                        <button wire:click="delete({{ $automation->id }})" wire:confirm="Are you sure you want to delete this automation?" class="flex-1 py-2 text-xs font-bold bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-2 sm:px-0">
            {{ $automations->links() }}
        </div>
    @else
        <div class="text-center py-16 sm:py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 mx-2 sm:mx-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-400">
                    <path d="M12 3v18"/>
                    <path d="M3 12h18"/>
                    <path d="m19 5-7 7-7-7"/>
                    <path d="m19 19-7-7-7 7"/>
                </svg>
            </div>
            <p class="text-zinc-500 mb-4 text-sm sm:text-base">No automations found.</p>
            <a href="{{ route('admin.ai-blog.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
                Create First Automation
            </a>
        </div>
    @endif
</div>
