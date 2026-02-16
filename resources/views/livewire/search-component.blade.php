<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl overflow-hidden border border-zinc-100 dark:border-zinc-800 w-full max-w-2xl mx-auto" x-data="{ focused: false }" @keydown.escape.window="$dispatch('close-search')">

    <!-- Header with Search Input -->
    <div class="relative p-6 pb-4 border-b border-zinc-100 dark:border-zinc-800">
        <div class="flex items-center gap-4">
            <!-- Search Icon -->
            <div class="w-12 h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>

            <!-- Input Field -->
            <div class="flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.200ms="query"
                    placeholder="Search anything..."
                    class="w-full text-lg font-medium bg-transparent border-none outline-none placeholder-zinc-400 text-zinc-900 dark:text-white"
                    x-ref="searchInput"
                    x-init="$nextTick(() => $refs.searchInput.focus())"
                >
            </div>

            <!-- Loading Spinner -->
            <div wire:loading wire:target="query" class="shrink-0">
                <svg class="animate-spin h-5 w-5 text-mint" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Close Button -->
            <button
                @click="$dispatch('close-search')"
                class="w-10 h-10 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 flex items-center justify-center transition-colors shrink-0 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/>
                    <path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- Type Filters -->
        <div class="flex items-center gap-2 mt-4 ml-16">
            <button
                wire:click="$set('type', 'all')"
                class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all duration-200 {{ $type === 'all' ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
            >
                All
            </button>
            <button
                wire:click="$set('type', 'projects')"
                class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all duration-200 {{ $type === 'projects' ? 'bg-violet text-white' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                    Projects
                </span>
            </button>
            <button
                wire:click="$set('type', 'blogs')"
                class="px-4 py-1.5 rounded-full text-sm font-semibold transition-all duration-200 {{ $type === 'blogs' ? 'bg-mint text-zinc-950' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
            >
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                    Blog
                </span>
            </button>
        </div>
    </div>

    <!-- Results Area -->
    <div class="max-h-[50vh] overflow-y-auto custom-scrollbar bg-zinc-50/50 dark:bg-zinc-950/30">
        @if (strlen($query) >= 2)
            @if (count($results) > 0)
                <div class="p-2">
                    @foreach ($results as $result)
                        @if ($result['type'] === 'project')
                            <a href="{{ route('projects.show', $result['data']->slug) }}"
                               wire:navigate
                               @click="$dispatch('close-search')"
                               class="group flex items-center gap-4 p-3 rounded-xl hover:bg-white dark:hover:bg-zinc-800 transition-all duration-200 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 hover:shadow-sm">

                                <!-- Project Thumbnail or Icon -->
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet/20 to-violet/5 flex items-center justify-center shrink-0 overflow-hidden">
                                    @if ($result['data']->thumbnail)
                                        <img src="{{ Storage::url($result['data']->thumbnail) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet">
                                            <path d="M7 7h10v10"/><path d="M7 17 17 7"/>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-violet bg-violet/10 px-2 py-0.5 rounded-full">Project</span>
                                        <span class="text-xs text-zinc-400">{{ $result['data']->category }}</span>
                                    </div>
                                    <h3 class="font-bold text-zinc-900 dark:text-white group-hover:text-violet transition-colors truncate">
                                        {{ $result['data']->title }}
                                    </h3>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-1 mt-0.5">
                                        {{ Str::limit(strip_tags($result['data']->description), 80) }}
                                    </p>
                                    @if ($result['data']->tech_stack)
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach (array_slice($result['data']->tech_stack, 0, 4) as $tech)
                                                <span class="px-2 py-0.5 text-[10px] font-medium bg-zinc-100 dark:bg-zinc-700 text-zinc-500 dark:text-zinc-400 rounded">
                                                    {{ $tech }}
                                                </span>
                                            @endforeach
                                            @if (count($result['data']->tech_stack) > 4)
                                                <span class="px-2 py-0.5 text-[10px] text-zinc-400">+{{ count($result['data']->tech_stack) - 4 }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>

                                <!-- Arrow -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300 group-hover:text-violet group-hover:translate-x-1 transition-all shrink-0">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </a>

                        @elseif ($result['type'] === 'blog')
                            <a href="{{ route('blog.show', $result['data']->slug) }}"
                               wire:navigate
                               @click="$dispatch('close-search')"
                               class="group flex items-center gap-4 p-3 rounded-xl hover:bg-white dark:hover:bg-zinc-800 transition-all duration-200 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 hover:shadow-sm">

                                <!-- Blog Image or Icon -->
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-mint/20 to-mint/5 flex items-center justify-center shrink-0 overflow-hidden">
                                    @if ($result['data']->image)
                                        <img src="{{ Storage::url($result['data']->image) }}" alt="" class="w-full h-full object-cover">
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-mint">
                                            <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/>
                                        </svg>
                                    @endif
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-[10px] font-black uppercase tracking-wider text-mint bg-mint/10 px-2 py-0.5 rounded-full">Blog</span>
                                        <span class="text-xs text-zinc-400">{{ $result['data']->category }}</span>
                                    </div>
                                    <h3 class="font-bold text-zinc-900 dark:text-white group-hover:text-mint transition-colors truncate">
                                        {{ $result['data']->title }}
                                    </h3>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-1 mt-0.5">
                                        {{ Str::limit(strip_tags($result['data']->excerpt), 80) }}
                                    </p>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-zinc-400">
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            {{ $result['data']->author }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                            {{ $result['data']->published_at?->format('M d, Y') }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                            {{ $result['data']->read_time }} min
                                        </span>
                                    </div>
                                </div>

                                <!-- Arrow -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-300 group-hover:text-mint group-hover:translate-x-1 transition-all shrink-0">
                                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>
                                </svg>
                            </a>
                        @endif
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center py-16 px-6">
                    <div class="w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">No results found</h3>
                    <p class="text-zinc-500 dark:text-zinc-400 text-center max-w-xs">
                        We couldn't find anything matching "<span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $query }}</span>". Try different keywords.
                    </p>
                </div>
            @endif
        @else
            <!-- Initial State -->
            <div class="flex flex-col items-center justify-center py-16 px-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-zinc-100 to-zinc-50 dark:from-zinc-800 dark:to-zinc-900 flex items-center justify-center mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-zinc-900 dark:text-white mb-2">Search everything</h3>
                <p class="text-zinc-500 dark:text-zinc-400 text-center text-sm">
                    Find projects, blog posts, and more. Type at least 2 characters to begin.
                </p>

                <!-- Quick Tips -->
                <div class="flex flex-wrap justify-center gap-2 mt-6">
                    <span class="px-3 py-1.5 text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-full">Try "Laravel"</span>
                    <span class="px-3 py-1.5 text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-full">Try "Design"</span>
                    <span class="px-3 py-1.5 text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 rounded-full">Try "API"</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="px-6 py-3 border-t border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900 flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-zinc-400">
            <span class="font-medium">{{ count($results) }}</span>
            <span>results found</span>
        </div>
        <div class="flex items-center gap-2 text-xs text-zinc-400">
            <span>Powered by</span>
            <span class="font-semibold text-mint">Algolia</span>
        </div>
    </div>
</div>
