<x-slot name="seo">
    <x-seo-meta
        :title="$post->title"
        :description="$post->meta_description ?? $post->excerpt"
        :keywords="$post->meta_keywords"
        type="article"
    />
</x-slot>

<main class="pt-32 pb-20 px-6 lg:px-12 max-w-4xl mx-auto">
    <!-- Back Link -->
    <a href="{{ route('blog.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7"/>
            <path d="M19 12H5"/>
        </svg>
        {{ __('frontend.blog.back_to_journal') }}
    </a>

    <!-- Article Header -->
    <header class="mb-12">
        <div class="flex items-center gap-3 mb-4">
            <span class="text-[10px] font-black uppercase tracking-widest text-mint">
                {{ $post->category }}
            </span>
            <span class="text-zinc-300 dark:text-zinc-700">•</span>
            <span class="text-xs text-zinc-500">{{ $post->published_at?->format('M d, Y') ?? $post->created_at?->format('M d, Y') }}</span>
            <span class="text-zinc-300 dark:text-zinc-700">•</span>
            <span class="text-xs text-zinc-500">{{ $post->read_time }} {{ __('frontend.blog.min_read') }}</span>
        </div>

        <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tighter mb-6 leading-tight">
            {{ $post->title }}
        </h1>

        <p class="text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed">
            {{ $post->excerpt }}
        </p>
    </header>

    <!-- Featured Image -->
    <div class="aspect-[21/9] rounded-3xl overflow-hidden mb-12 bg-zinc-100 dark:bg-zinc-800">
        @if($post->image)
            <img
                src="{{ Storage::url($post->image) }}"
                alt="{{ $post->title }}"
                class="w-full h-full object-cover"
            >
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                    <circle cx="9" cy="9" r="2"/>
                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                </svg>
            </div>
        @endif
    </div>

    <!-- Article Content -->
    <article class="prose prose-lg dark:prose-invert max-w-none mb-20">
        {!! $post->content !!}
    </article>

    <!-- Author Section -->
    <div class="flex items-center gap-4 py-8 border-y border-zinc-100 dark:border-zinc-900 mb-16">
        <div class="w-14 h-14 rounded-full bg-mint flex items-center justify-center text-zinc-950 font-black text-lg">
            {{ substr($post->author ?? 'A', 0, 1) }}
        </div>
        <div>
            <p class="font-bold">{{ $post->author ?? 'Admin' }}</p>
            <p class="text-sm text-zinc-500">{{ __('frontend.blog.author_role') }}</p>
        </div>
    </div>

    <!-- Related Articles -->
    @if($relatedPosts->count() > 0)
        <div class="mb-12">
            <h3 class="text-2xl font-bold mb-8">{{ __('frontend.blog.related_articles') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($relatedPosts as $related)
                    <a href="{{ route('blog.show', $related->slug) }}" wire:navigate class="group">
                        <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-4 bg-zinc-100 dark:bg-zinc-800">
                            @if($related->image)
                                <img
                                    src="{{ Storage::url($related->image) }}"
                                    alt="{{ $related->title }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    loading="lazy"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                                        <circle cx="9" cy="9" r="2"/>
                                        <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-[10px] font-black uppercase tracking-widest text-mint">{{ $related->category }}</span>
                            <span class="text-zinc-300 dark:text-zinc-700">•</span>
                            <span class="text-xs text-zinc-500">{{ $related->read_time }} {{ __('frontend.blog.min_read') }}</span>
                        </div>
                        <h4 class="text-lg font-bold group-hover:text-mint transition-colors">{{ $related->title }}</h4>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- CTA -->
    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-3xl p-8 md:p-12 text-center">
        <h3 class="text-2xl font-bold mb-4">{{ __('frontend.blog.enjoyed_article') }}</h3>
        <p class="text-zinc-500 dark:text-zinc-400 mb-6">{{ __('frontend.blog.discuss_project') }}</p>
        <a href="{{ route('contact.index') }}" wire:navigate class="inline-flex items-center gap-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-6 py-3 rounded-full font-bold hover:scale-105 transition-transform">
            {{ __('frontend.blog.get_in_touch') }}
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="m12 5 7 7-7 7"/>
            </svg>
        </a>
    </div>
</main>
