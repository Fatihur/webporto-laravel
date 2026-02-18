<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Experience</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Manage your work history</p>
        </div>
        <a href="{{ route('admin.experiences.create') }}" wire:navigate
           class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="M12 5v14"/>
            </svg>
            <span class="hidden sm:inline">Add Experience</span>
            <span class="sm:hidden">Add</span>
        </a>
    </div>

    @if(count($experiences) > 0)
        <!-- Bulk Actions Bar -->
        @if(count($selected) > 0)
            <div class="bg-mint/10 dark:bg-mint/20 border border-mint/30 rounded-xl p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ count($selected) }} selected</span>
                    <div class="flex items-center gap-2">
                        <select wire:model="bulkAction" class="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm focus:ring-2 focus:ring-mint">
                            <option value="">Select Action</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button wire:click="executeBulkAction" wire:confirm="Are you sure you want to execute this action?"
                                class="px-4 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">
                            Execute
                        </button>
                    </div>
                </div>
                <button wire:click="$set('selected', [])" class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                    Clear selection
                </button>
            </div>
        @endif

        <!-- Skeleton Loading - Desktop -->
        <div wire:loading.delay class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-4 py-4 text-left">
                        <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Order</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Company</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Period</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td class="px-4 py-4">
                            <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                                <div class="w-6 h-4 bg-zinc-200 dark:bg-zinc-700 rounded animate-pulse"></div>
                                <div class="w-6 h-6 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-28 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16 mx-auto animate-pulse"></div>
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
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-4 h-4 mt-1 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                        <div class="flex-1 space-y-2 min-w-0">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 animate-pulse"></div>
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2 animate-pulse"></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
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
                    <th class="px-4 py-4 text-left">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Order</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Company</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Role</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Period</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($experiences as $experience)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $experience->id }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $experience->id }}" wire:model.live="selected" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button wire:click="moveUp({{ $experience->id }})"
                                        class="p-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors {{ $loop->first ? 'opacity-30 cursor-not-allowed' : '' }}"
                                    {{ $loop->first ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m18 15-6-6-6 6"/>
                                    </svg>
                                </button>
                                <span class="font-mono text-sm">{{ $experience->order }}</span>
                                <button wire:click="moveDown({{ $experience->id }})"
                                        class="p-1 hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded transition-colors {{ $loop->last ? 'opacity-30 cursor-not-allowed' : '' }}"
                                    {{ $loop->last ? 'disabled' : '' }}>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-bold">{{ $experience->company }}</td>
                        <td class="px-6 py-4">{{ $experience->role }}</td>
                        <td class="px-6 py-4 text-zinc-500 text-sm">
                            {{ $experience->dateRange() }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($experience->is_current)
                                <span class="px-3 py-1 bg-mint/10 text-mint text-xs font-bold rounded-full">Current</span>
                            @else
                                <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-xs font-bold rounded-full">Past</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.experiences.edit', $experience->id) }}" wire:navigate
                                   class="text-sm font-bold hover:text-mint transition-colors">Edit</a>
                                <button wire:click="delete({{ $experience->id }})"
                                        wire:confirm="Are you sure you want to delete this experience?"
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
            @foreach($experiences as $experience)
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800" wire:key="mobile-{{ $experience->id }}">
                    <!-- Header: Company + Status -->
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex items-start gap-3 flex-1 min-w-0">
                            <input type="checkbox" value="{{ $experience->id }}" wire:model.live="selected" class="mt-1 rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint flex-shrink-0">
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-sm line-clamp-1">{{ $experience->company }}</h3>
                                <p class="text-xs text-zinc-500">{{ $experience->role }}</p>
                            </div>
                        </div>
                        @if($experience->is_current)
                            <span class="px-2 py-0.5 bg-mint/10 text-mint text-xs font-bold rounded-full flex-shrink-0">Current</span>
                        @else
                            <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-500 text-xs font-bold rounded-full flex-shrink-0">Past</span>
                        @endif
                    </div>

                    <!-- Period -->
                    <div class="text-xs text-zinc-500 mb-3 ml-7">
                        {{ $experience->dateRange() }}
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800 ml-7">
                        <!-- Reorder buttons -->
                        <div class="flex items-center gap-1 mr-auto">
                            <button wire:click="moveUp({{ $experience->id }})" class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors {{ $loop->first ? 'opacity-30 cursor-not-allowed' : '' }}" {{ $loop->first ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-500">
                                    <path d="m18 15-6-6-6 6"/>
                                </svg>
                            </button>
                            <span class="font-mono text-sm text-zinc-500">{{ $experience->order }}</span>
                            <button wire:click="moveDown({{ $experience->id }})" class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors {{ $loop->last ? 'opacity-30 cursor-not-allowed' : '' }}" {{ $loop->last ? 'disabled' : '' }}>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-500">
                                    <path d="m6 9 6 6 6-6"/>
                                </svg>
                            </button>
                        </div>

                        <a href="{{ route('admin.experiences.edit', $experience->id) }}" wire:navigate class="px-4 py-2 text-xs font-bold bg-mint/10 text-mint rounded-lg hover:bg-mint/20 transition-colors">
                            Edit
                        </a>
                        <button wire:click="delete({{ $experience->id }})" wire:confirm="Are you sure you want to delete this experience?" class="px-4 py-2 text-xs font-bold bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-2 sm:px-0">
            {{ $experiences->links() }}
        </div>
    @else
        <div class="text-center py-16 sm:py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 mx-2 sm:mx-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-400">
                    <rect width="20" height="14" x="2" y="7" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
            </div>
            <p class="text-zinc-500 mb-4 text-sm sm:text-base">No experience entries yet.</p>
            <a href="{{ route('admin.experiences.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
                Add First Experience
            </a>
        </div>
    @endif
</div>
