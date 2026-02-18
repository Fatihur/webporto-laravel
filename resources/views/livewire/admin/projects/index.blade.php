<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Projects</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Manage your portfolio projects</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" wire:navigate
           class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="M12 5v14"/>
            </svg>
            <span class="hidden sm:inline">Add Project</span>
            <span class="sm:hidden">Add</span>
        </a>
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
                    <input type="text" wire:model.live="search" placeholder="Search projects..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                </div>
            </div>

            <!-- Category Filter -->
            <div class="sm:w-48">
                <select wire:model.live="categoryFilter"
                        class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ ucwords(str_replace('-', ' ', $cat)) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Skeleton Loading - Desktop -->
    <div wire:loading.delay class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-4 py-4 text-left">
                        <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Title</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Category</th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Date</th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Featured</th>
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
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                                <div class="space-y-2">
                                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                                    <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="h-4 w-4 mx-auto bg-zinc-200 dark:bg-zinc-700 rounded animate-pulse"></div>
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
                    <div class="w-12 h-12 rounded-lg bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                    <div class="flex-1 space-y-2 min-w-0">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-12 animate-pulse"></div>
                </div>
                <div class="flex gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex-1 h-9 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                    <div class="flex-1 h-9 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                </div>
            </div>
        @endfor
    </div>

    @if(count($projects) > 0)
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

        <!-- Desktop Table -->
        <div wire:loading.remove class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-4 py-4 text-left text-sm font-bold">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors"
                        wire:click="sortBy('title')">
                        Title
                        @if($sortField === 'title')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors"
                        wire:click="sortBy('category')">
                        Category
                        @if($sortField === 'category')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors"
                        wire:click="sortBy('project_date')">
                        Date
                        @if($sortField === 'project_date')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Featured</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($projects as $project)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $project->id }}">
                        <td class="px-4 py-4">
                            <input type="checkbox" value="{{ $project->id }}" wire:model.live="selected" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($project->thumbnail)
                                    <img src="{{ Storage::url($project->thumbnail) }}" alt=""
                                         class="w-10 h-10 rounded-lg object-cover">
                                @endif
                                <div>
                                    <div class="font-bold">{{ $project->title }}</div>
                                    <div class="text-xs text-zinc-500">{{ Str::limit($project->slug, 30) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 bg-mint/10 text-mint text-xs font-bold rounded-full uppercase tracking-wide">
                                    {{ str_replace('-', ' ', $project->category) }}
                                </span>
                        </td>
                        <td class="px-6 py-4 text-zinc-500">
                            {{ $project->project_date?->format('M Y') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($project->is_featured)
                                <span class="text-mint">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="currentColor" stroke="currentColor"
                                             stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polygon
                                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                        </svg>
                                    </span>
                            @else
                                <span class="text-zinc-300">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.projects.edit', $project->id) }}" wire:navigate
                                   class="text-sm font-bold hover:text-mint transition-colors">Edit</a>
                                <button wire:click="delete({{ $project->id }})"
                                        wire:confirm="Are you sure you want to delete this project?"
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
            @foreach($projects as $project)
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800" wire:key="mobile-{{ $project->id }}">
                    <!-- Header: Thumbnail + Title + Checkbox -->
                    <div class="flex items-start gap-3 mb-3">
                        <input type="checkbox" value="{{ $project->id }}" wire:model.live="selected" class="mt-1 rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                        @if($project->thumbnail)
                            <img src="{{ Storage::url($project->thumbnail) }}" alt="" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-sm line-clamp-2">{{ $project->title }}</h3>
                            <p class="text-xs text-zinc-500 truncate">{{ $project->slug }}</p>
                        </div>
                        @if($project->is_featured)
                            <span class="text-mint flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </span>
                        @endif
                    </div>

                    <!-- Meta Info -->
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-2 py-0.5 bg-mint/10 text-mint text-xs font-bold rounded-full uppercase">
                            {{ str_replace('-', ' ', $project->category) }}
                        </span>
                        <span class="text-xs text-zinc-500">{{ $project->project_date?->format('M Y') }}</span>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <a href="{{ route('admin.projects.edit', $project->id) }}" wire:navigate class="flex-1 py-2.5 text-xs font-bold bg-mint/10 text-mint rounded-lg text-center hover:bg-mint/20 transition-colors">
                            Edit
                        </a>
                        <button wire:click="delete({{ $project->id }})" wire:confirm="Are you sure you want to delete this project?" class="flex-1 py-2.5 text-xs font-bold bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-2 sm:px-0">
            {{ $projects->links() }}
        </div>
    </div>

    @else
        <div
            class="text-center py-16 sm:py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 mx-2 sm:mx-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-400">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <p class="text-zinc-500 mb-4 text-sm sm:text-base">No projects found.</p>
            <a href="{{ route('admin.projects.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
                Create First Project
            </a>
        </div>
    @endif
</div>
