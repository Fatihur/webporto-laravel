<x-slot name="seo">
    <x-seo-meta
        :title="$title"
        :description="$description"
        keywords="portfolio projects, software project, design project, case study"
        :url="$selectedCategory ? route('projects.category', $selectedCategory) : route('projects.category', 'graphic-design')"
    />

    @if(!empty($structuredData))
        @foreach($structuredData as $schema)
            <x-structured-data :data="$schema" />
        @endforeach
    @endif
</x-slot>

<main class="pt-32 pb-20 px-6 lg:px-12 max-w-7xl mx-auto" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
    <!-- Header -->
    <header class="mb-12 transition-all duration-1000" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <!-- Breadcrumb -->
        <div class="flex items-center gap-3 text-zinc-400 font-bold mb-4">
            <a href="{{ route('home') }}" wire:navigate class="hover:text-zinc-950 dark:hover:text-white transition-colors">Home</a>
            <span>/</span>
            <span class="text-zinc-950 dark:text-white">{{ $title }}</span>
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter mb-6">
            {{ $title }}
        </h1>
        <p class="text-xl text-zinc-500 dark:text-zinc-400 max-w-2xl leading-relaxed mb-8">
            {{ $description }}
        </p>

        <!-- Search Bar -->
        <div class="relative max-w-lg mb-6">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search projects..."
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
            <p class="mb-4 text-sm text-zinc-500">
                Found {{ $projects->count() }} {{ $projects->count() == 1 ? 'result' : 'results' }} for "{{ $search }}"
            </p>
        @endif

        <!-- Category Filter Buttons -->
        <div class="flex flex-wrap gap-3">
            <button
                wire:click="filterByCategory(null)"
                class="px-5 py-2.5 rounded-full text-sm font-bold transition-all focus:outline-none focus:ring-2 focus:ring-mint focus:ring-offset-2 dark:focus:ring-offset-zinc-950 {{ $selectedCategory === null ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800' }}"
            >
                All Projects
            </button>
            @foreach($categories as $cat)
                <button
                    wire:click="filterByCategory('{{ $cat['id'] }}')"
                    class="px-5 py-2.5 rounded-full text-sm font-bold transition-all focus:outline-none focus:ring-2 focus:ring-mint focus:ring-offset-2 dark:focus:ring-offset-zinc-950 {{ $selectedCategory === $cat['id'] ? 'bg-zinc-950 dark:bg-white text-white dark:text-zinc-950' : 'bg-zinc-100 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-800' }}"
                >
                    {{ $cat['name'] }}
                </button>
            @endforeach
        </div>
    </header>

    <!-- Loading Skeleton -->
    <div wire:loading.delay class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @for($i = 0; $i < 6; $i++)
            <x-skeleton.card image title description meta />
        @endfor
    </div>

    <!-- Projects Grid -->
    <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-all duration-1000 delay-300 transform" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
        @if($projects->count() > 0)
            @foreach($projects as $project)
                <a
                    href="{{ route('projects.show', $project->slug) }}"
                    wire:navigate
                    class="group block bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] overflow-hidden transition-all duration-300"
                >
                    <!-- Thumbnail -->
                    <div class="aspect-[4/3] overflow-hidden">
                        @if($project->thumbnail)
                            <x-optimized-image
                                :src="Storage::url($project->thumbnail)"
                                :alt="$project->title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                            />
                        @else
                            <div class="w-full h-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-8">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-[10px] font-black uppercase tracking-widest text-mint">{{ $project->category }}</span>
                            <span class="text-zinc-300 dark:text-zinc-700">•</span>
                            <span class="text-xs text-zinc-500">{{ $project->project_date?->format('M Y') ?? $project->created_at?->format('M Y') }}</span>
                        </div>

                        <h3 class="text-xl font-bold mb-3 group-hover:text-mint transition-colors">{{ $project->title }}</h3>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed">{!! Str::limit(strip_tags($project->description), 120) !!}</p>

                        @if($project->tags)
                            <div class="flex flex-wrap gap-2 mt-4">
                                @foreach(array_slice($project->tags, 0, 3) as $tag)
                                    <span class="px-3 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-full text-[10px] font-bold text-zinc-500">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        @else
            <div class="col-span-full py-20 text-center bg-zinc-50 dark:bg-zinc-900 rounded-4xl">
                <div class="w-16 h-16 mx-auto mb-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold mb-2">No results found</h3>
                <p class="text-zinc-500 dark:text-zinc-400 mb-6">
                    @if($search)
                        No results found for "{{ $search }}"
                    @else
                        No projects found in this category
                    @endif
                </p>
                @if($search)
                    <button
                        wire:click="clearSearch"
                        class="px-6 py-3 rounded-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold hover:scale-105 transition-transform"
                    >
                        Clear Search
                    </button>
                @endif
            </div>
        @endif
    </div>

</main>
