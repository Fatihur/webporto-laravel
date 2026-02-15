<main class="pt-32 pb-20 px-6 lg:px-12 max-w-5xl mx-auto">
    <!-- Header -->
    <header class="mb-16 text-center max-w-3xl mx-auto">
        <span class="text-mint font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">Blog</span>
        <h1 class="text-5xl md:text-6xl font-extrabold tracking-tighter mb-6">Journal</h1>
        <p class="text-lg text-zinc-500 dark:text-zinc-400 mb-10">
            Thoughts on design, engineering, and the intersection of technology and minimalism.
        </p>

        <!-- Search Bar -->
        <div class="relative max-w-lg mx-auto">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search articles..."
                class="w-full pl-11 pr-10 py-3.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
            >
            @if($search)
                <button
                    wire:click="clearSearch"
                    class="absolute inset-y-0 right-3 flex items-center p-1.5 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            @endif
        </div>

        <!-- Results Count -->
        @if($search)
            <p class="mt-4 text-sm text-zinc-500">
                Found {{ $posts->total() }} result{{ $posts->total() !== 1 ? 's' : '' }} for "{{ $search }}"
            </p>
        @endif

        <!-- Category Filter -->
        @if($categories->count() > 0)
            <div class="flex flex-wrap justify-center gap-2 mt-6">
                <button
                    wire:click="$set('category', '')"
                    class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ $category === '' ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800' }}"
                >
                    All
                </button>
                @foreach($categories as $cat)
                    <button
                        wire:click="$set('category', '{{ $cat }}')"
                        class="px-4 py-2 rounded-full text-xs font-bold transition-all {{ $category === $cat ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800' }}"
                    >
                        {{ ucwords($cat) }}
                    </button>
                @endforeach
            </div>
        @endif
    </header>

    <!-- Articles List -->
    <div class="space-y-12">
        @forelse($posts as $post)
            <article class="group">
                <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="flex flex-col md:flex-row gap-6 md:gap-10 items-start">
                    <!-- Image -->
                    <div class="w-full md:w-72 lg:w-80 shrink-0">
                        <div class="aspect-[16/10] md:aspect-[4/3] rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            @if($post->image)
                                <img
                                    src="{{ Storage::url($post->image) }}"
                                    alt="{{ $post->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                                        <circle cx="9" cy="9" r="2"/>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 py-2">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="text-[10px] font-black uppercase tracking-widest text-mint">
                                {{ $post->category }}
                            </span>
                            <span class="text-zinc-300 dark:text-zinc-700">•</span>
                            <span class="text-xs text-zinc-500">{{ $post->published_at?->format('M d, Y') ?? $post->created_at->format('M d, Y') }}</span>
                            <span class="text-zinc-300 dark:text-zinc-700">•</span>
                            <span class="text-xs text-zinc-500">{{ $post->read_time }} min read</span>
                        </div>

                        <h2 class="text-2xl md:text-3xl font-bold mb-3 group-hover:text-mint transition-colors leading-tight">
                            {{ $post->title }}
                        </h2>

                        <p class="text-zinc-600 dark:text-zinc-400 leading-relaxed mb-4 line-clamp-2">
                            {{ $post->excerpt }}
                        </p>

                        <span class="inline-flex items-center gap-2 text-sm font-bold text-zinc-950 dark:text-white group-hover:gap-3 transition-all">
                            Read Article
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14"/>
                                <path d="m12 5 7 7-7 7"/>
                            </svg>
                        </span>
                    </div>
                </a>
            </article>

            <!-- Divider -->
            @if(!$loop->last)
                <hr class="border-zinc-100 dark:border-zinc-800">
            @endif

        @empty
            <!-- Empty State -->
            <div class="py-20 text-center">
                <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-zinc-100 dark:bg-zinc-900 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">No articles found</h3>
                <p class="text-zinc-500 dark:text-zinc-400 mb-6">Try adjusting your search terms</p>
                <button
                    wire:click="clearSearch"
                    class="px-6 py-3 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold hover:scale-105 transition-transform"
                >
                    Clear Search
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-16">
        {{ $posts->links() }}
    </div>
</main>
