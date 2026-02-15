<x-slot name="seo">
    <x-seo-meta
        :title="$project->title"
        :description="$project->meta_description ?? $project->description"
        :keywords="$project->meta_keywords"
        type="article"
    />
</x-slot>

<main class="pt-32 pb-20 px-6 lg:px-12 max-w-5xl mx-auto">
    <a href="{{ route('projects.category', $project->category) }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
        {{ __('frontend.projects.back_to', ['category' => str_replace('-', ' ', $project->category)]) }}
    </a>

    <header class="mb-16">
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter mb-8">{{ $project->title }}</h1>

        <!-- Share Buttons -->
        <div class="mb-6">
            <x-share-buttons :url="request()->url()" :title="$project->title" :description="$project->description" size="sm" />
        </div>

        <div class="flex flex-wrap items-center gap-8 py-8 border-y border-zinc-100 dark:border-zinc-800">
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                <div>
                    <p class="text-[10px] font-black uppercase text-zinc-400">{{ __('frontend.projects.date') }}</p>
                    <p class="text-sm font-bold">{{ $project->project_date?->format('M Y') ?? $project->created_at?->format('M Y') }}</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg>
                <div>
                    <p class="text-[10px] font-black uppercase text-zinc-400">{{ __('frontend.projects.category') }}</p>
                    <p class="text-sm font-bold capitalize">{{ str_replace('-', ' ', $project->category) }}</p>
                </div>
            </div>
            <div class="flex-1"></div>
            <div class="flex gap-4">
                @if($project->link)
                    <a href="{{ $project->link }}" target="_blank" rel="noopener noreferrer"
                       class="flex items-center gap-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-6 py-3 rounded-full text-sm font-bold hover:scale-105 transition-transform">
                        {{ __('frontend.projects.launch') }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/></svg>
                    </a>
                @endif
                <button class="p-3 border border-zinc-200 dark:border-zinc-800 rounded-full hover:bg-zinc-50 dark:hover:bg-zinc-900">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                </button>
            </div>
        </div>
    </header>

    <div class="aspect-video w-full rounded-[2.5rem] overflow-hidden mb-16 shadow-2xl shadow-zinc-200 dark:shadow-none">
        @if($project->thumbnail)
            <img src="{{ Storage::url($project->thumbnail) }}" class="w-full h-full object-cover" alt="{{ $project->title }}" loading="lazy">
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

    <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
        <div class="md:col-span-8">
            <h2 class="text-3xl font-bold mb-6">{{ __('frontend.projects.overview') }}</h2>
            <div class="prose dark:prose-invert max-w-none">
                {!! $project->content !!}
            </div>

            {{-- Gallery Section --}}
            @if($project->gallery && count($project->gallery) > 0)
                <div class="mt-16 pt-16 border-t border-zinc-100 dark:border-zinc-800" x-data="{ open: false, currentImage: '' }">
                    <h3 class="text-2xl font-bold mb-8">{{ __('frontend.projects.gallery') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($project->gallery as $index => $img)
                            <div class="rounded-3xl overflow-hidden group cursor-pointer"
                                 @click="open = true; currentImage = '{{ Storage::url($img) }}'">
                                <img src="{{ Storage::url($img) }}"
                                     class="w-full h-72 object-cover group-hover:scale-105 transition-transform duration-700"
                                     alt="Gallery image {{ $index + 1 }}"
                                     loading="lazy"
                                >
                            </div>
                        @endforeach
                    </div>

                    {{-- Lightbox Modal --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         @keydown.escape.window="open = false"
                         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/90"
                         style="display: none;"
                    >
                        {{-- Close Button --}}
                        <button @click="open = false" class="absolute top-6 right-6 text-white hover:text-mint transition-colors z-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>

                        {{-- Image --}}
                        <img :src="currentImage"
                             class="max-w-full max-h-[90vh] object-contain rounded-2xl"
                             @click.stop
                        >

                        {{-- Click outside to close --}}
                        <div @click="open = false" class="absolute inset-0 -z-10"></div>
                    </div>
                </div>
            @endif
        </div>

        <div class="md:col-span-4 space-y-10">
            @if($project->tags && count($project->tags) > 0)
                <div>
                    <h4 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-6">{{ __('frontend.projects.core_tags') }}</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($project->tags as $tag)
                            <span class="px-4 py-2 bg-zinc-100 dark:bg-zinc-900 rounded-full text-xs font-bold">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($project->tech_stack && count($project->tech_stack) > 0)
                <div>
                    <h4 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-6">{{ __('frontend.projects.stack') }}</h4>
                    <ul class="space-y-3">
                        @foreach($project->tech_stack as $tech)
                            <li class="flex items-center gap-3 text-sm font-bold">
                                <div class="w-2 h-2 rounded-full bg-mint"></div>
                                {{ $tech }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($project->stats && count($project->stats) > 0)
                <div class="bg-zinc-50 dark:bg-zinc-900 p-8 rounded-4xl">
                    <h4 class="text-sm font-black uppercase tracking-widest text-zinc-400 mb-6">{{ __('frontend.projects.key_results') }}</h4>
                    <div class="space-y-6">
                        @foreach($project->stats as $stat)
                            @if(isset($stat['label']) && isset($stat['value']))
                                <div>
                                    <p class="text-xs font-bold text-zinc-400 mb-1">{{ $stat['label'] }}</p>
                                    <p class="text-2xl font-black">{{ $stat['value'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Related Projects --}}
    @if($relatedProjects->count() > 0)
        <div class="mt-20 pt-20 border-t border-zinc-100 dark:border-zinc-800">
            <h3 class="text-2xl font-bold mb-8">{{ __('frontend.projects.related_projects') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($relatedProjects as $related)
                    <a href="{{ route('projects.show', $related->slug) }}" wire:navigate class="group">
                        <div class="aspect-[4/3] rounded-2xl overflow-hidden mb-4">
                            @if($related->thumbnail)
                                <img src="{{ Storage::url($related->thumbnail) }}" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-zinc-200 dark:bg-zinc-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                                        <circle cx="9" cy="9" r="2"/>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <h4 class="font-bold group-hover:text-mint transition-colors">{{ $related->title }}</h4>
                        <p class="text-sm text-zinc-500">{{ str_replace('-', ' ', $related->category) }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</main>
