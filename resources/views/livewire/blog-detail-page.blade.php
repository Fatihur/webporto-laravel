<x-slot name="seo">
    <x-seo-meta
        :title="$post->meta_title ?: $post->title"
        :description="$post->meta_description ?? $post->excerpt"
        :keywords="$post->meta_keywords"
        :image="$post->image_url ?? ($post->image ? Storage::url($post->image) : null)"
        :url="route('blog.show', $post->slug)"
        type="article"
        :published-time="$post->published_at?->toIso8601String()"
        :modified-time="$post->updated_at->toIso8601String()"
    />

    @if(!empty($structuredData))
        @foreach($structuredData as $schema)
            <x-structured-data :data="$schema" />
        @endforeach
    @endif
</x-slot>

<main class="pt-32 pb-20 px-4 sm:px-6 lg:px-12 max-w-4xl mx-auto overflow-x-hidden" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
    <!-- Reading Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1 bg-zinc-200 dark:bg-zinc-800 z-50">
        <div class="h-full bg-mint transition-all ease-out" 
             x-data="{ scroll: 0 }" 
             @scroll.window="scroll = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100" 
             :style="`width: ${scroll}%`">
        </div>
    </div>

    <!-- Back Link -->
    <a href="{{ route('blog.index') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-bold text-zinc-500 hover:text-zinc-950 dark:hover:text-white mb-8 transition-all duration-1000 transform" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m12 19-7-7 7-7"/>
            <path d="M19 12H5"/>
        </svg>
        Back to Journal
    </a>

    <!-- Article Header -->
    <header class="mb-12 transition-all duration-1000 delay-150 transform" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
        <div class="flex flex-wrap items-center gap-2 sm:gap-3 mb-4">
            <span class="text-[10px] font-black uppercase tracking-widest text-mint">
                {{ $post->category }}
            </span>
            <span class="text-zinc-300 dark:text-zinc-700 hidden sm:inline">•</span>
            <span class="text-xs text-zinc-500">{{ $post->published_at?->format('M d, Y') ?? $post->created_at?->format('M d, Y') }}</span>
            <span class="text-zinc-300 dark:text-zinc-700 hidden sm:inline">•</span>
            <span class="text-xs text-zinc-600 dark:text-zinc-300">{{ $post->read_time }} min read</span>
            <span class="text-zinc-300 dark:text-zinc-700 hidden sm:inline">•</span>
            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-300">Math rendering enabled</span>
        </div>

        <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-extrabold tracking-tighter mb-6 leading-tight break-words">
            {{ $post->title }}
        </h1>

        <p class="text-lg sm:text-xl text-zinc-500 dark:text-zinc-400 leading-relaxed break-words">
            {!! $post->excerpt !!}
        </p>

        <!-- Share Buttons -->
        <div class="mt-6">
            <x-share-buttons :url="request()->url()" :title="$post->title" :description="$post->excerpt" size="sm" />
        </div>
    </header>

    <!-- Featured Image -->
    <div class="aspect-[21/9] rounded-3xl overflow-hidden mb-4 bg-zinc-100 dark:bg-zinc-800 transition-all duration-1000 delay-300 transform" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
        @if($post->image || $post->image_url)
            <x-optimized-image
                :src="$post->image_url ?? Storage::url($post->image)"
                :alt="$post->title"
                class="w-full h-full object-cover"
                priority
            />
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

    <!-- Image Source Attribution -->
    @if($post->image_source)
        <p class="text-xs text-zinc-500 text-right mb-8">
            Image source: {{ $post->image_source }}
        </p>
    @endif

    <!-- Article Content & TOC -->
    <div class="flex flex-col lg:flex-row gap-8 lg:gap-16 mb-12">
        <article class="prose prose-sm sm:prose-base md:prose-lg dark:prose-invert max-w-none flex-1 transition-all duration-1000 delay-500 transform" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'"
                 x-data="{
                    headings: [],
                    slugify(text) {
                        return text
                            .toLowerCase()
                            .trim()
                            .replace(/[^a-z0-9]+/g, '-')
                            .replace(/(^-|-$)+/g, '');
                    },
                    getUniqueHeadingId(baseId, usedIds, fallbackIndex) {
                        let normalized = (baseId || '').trim();

                        if(!normalized) {
                            normalized = 'heading-' + fallbackIndex;
                        }

                        let uniqueId = normalized;
                        let suffix = 2;

                        while(usedIds.has(uniqueId)) {
                            uniqueId = `${normalized}-${suffix}`;
                            suffix++;
                        }

                        return uniqueId;
                    },
                    init() {
                        this.$nextTick(() => {
                            // Lightbox
                            this.$el.querySelectorAll('img').forEach(img => {
                                img.style.cursor = 'zoom-in';
                                img.addEventListener('click', () => {
                                    $dispatch('open-lightbox', img.src);
                                });
                            });
                            
                            // Copy Code
                            this.$el.querySelectorAll('pre').forEach(pre => {
                                pre.style.position = 'relative';
                                let btn = document.createElement('button');
                                btn.className = 'absolute top-2 right-2 p-2.5 min-h-10 min-w-10 rounded-md bg-white/10 hover:bg-white/20 text-zinc-300 hover:text-white transition-colors text-xs flex items-center justify-center backdrop-blur-sm z-10 group opacity-0 group-hover/pre:opacity-100 transition-opacity focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-mint';
                                btn.innerHTML = `<svg class='w-4 h-4' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z'/></svg>`;
                                btn.onclick = (e) => {
                                    e.stopPropagation();
                                    let clone = pre.cloneNode(true);
                                    let b = clone.querySelector('button');
                                    if(b) b.remove();
                                    navigator.clipboard.writeText(clone.innerText.trim());
                                    
                                    let originalHtml = btn.innerHTML;
                                    btn.innerHTML = `<svg class='w-4 h-4 text-mint' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 13l4 4L19 7'/></svg>`;
                                    $dispatch('notify', {message: 'Code copied to clipboard!', type: 'success'});
                                    setTimeout(() => { btn.innerHTML = originalHtml; }, 2000);
                                };
                                pre.classList.add('group/pre');
                                pre.appendChild(btn);
                            });

                            // TOC
                            this.headings = [];
                            let headers = this.$el.querySelectorAll('h2, h3');
                            let usedIds = new Set();
                            headers.forEach((h, i) => {
                                if(!h.id) {
                                    h.id = this.slugify(h.innerText);
                                }

                                h.id = this.getUniqueHeadingId(h.id, usedIds, i);
                                usedIds.add(h.id);

                                this.headings.push({
                                    id: h.id,
                                    text: h.innerText,
                                    level: h.tagName.toLowerCase()
                                });
                            });

                            window.dispatchEvent(new CustomEvent('toc-generated', {
                                detail: [...this.headings],
                            }));
                        });
                    }
                 }"
        >
            <div class="break-words overflow-x-auto">
                {!! $post->content !!}
            </div>
        </article>

        <!-- TOC Sidebar -->
        <aside class="hidden lg:block w-64 shrink-0 transition-all duration-1000 delay-500 transform" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <div class="sticky top-32" x-data="{ 
                headings: [], 
                activeId: '',
                syncHeadings(headings) {
                    this.headings = Array.isArray(headings) ? headings : [];

                    if(this.headings.length === 0) {
                        this.activeId = '';
                        return;
                    }

                    if(!this.headings.some(h => h.id === this.activeId)) {
                        this.activeId = this.headings[0].id;
                    }

                    this.updateActiveHeading();
                },
                generateTocFromDom() {
                    let headers = document.querySelectorAll('article.prose h2, article.prose h3');

                    if(headers.length === 0) {
                        return;
                    }

                    let headings = [];
                    let usedIds = new Set();

                    headers.forEach((h, i) => {
                        let id = (h.id || '').trim();

                        if(!id) {
                            id = h.innerText.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                        }

                        if(!id) {
                            id = 'heading-' + i;
                        }

                        let uniqueId = id;
                        let suffix = 2;

                        while(usedIds.has(uniqueId)) {
                            uniqueId = `${id}-${suffix}`;
                            suffix++;
                        }

                        if(h.id !== uniqueId) {
                            h.id = uniqueId;
                        }

                        usedIds.add(uniqueId);

                        headings.push({
                            id: uniqueId,
                            text: h.innerText,
                            level: h.tagName.toLowerCase()
                        });
                    });

                    this.syncHeadings(headings);
                },
                updateActiveHeading() {
                    if(this.headings.length === 0) {
                        this.activeId = '';
                        return;
                    }

                    let scrollPosition = window.scrollY + 140;
                    let current = this.headings[0].id;

                    for (let h of this.headings) {
                        let el = document.getElementById(h.id);

                        if (!el) {
                            continue;
                        }

                        let headingTop = el.getBoundingClientRect().top + window.scrollY;

                        if (headingTop <= scrollPosition) {
                            current = h.id;
                        } else {
                            break;
                        }
                    }

                    this.activeId = current;
                },
                init() {
                    window.addEventListener('toc-generated', (e) => {
                        this.syncHeadings(e.detail);
                    });
                    
                    window.addEventListener('scroll', () => this.updateActiveHeading(), { passive: true });
                    window.addEventListener('resize', () => this.updateActiveHeading(), { passive: true });

                    this.$nextTick(() => {
                        requestAnimationFrame(() => {
                            if(this.headings.length === 0) {
                                this.generateTocFromDom();
                            }

                            this.updateActiveHeading();
                        });
                    });
                },
                scrollTo(id) {
                    let el = document.getElementById(id);
                    if(el) {
                        window.scrollTo({
                            top: el.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }
            }">
                <template x-if="headings.length > 0">
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-4 px-2">Table of Contents</h4>
                        <nav class="space-y-1 border-l border-zinc-200 dark:border-zinc-800">
                            <template x-for="h in headings" :key="h.id">
                                <a :href="'#' + h.id"
                                   @click.prevent="scrollTo(h.id)"
                                   class="block px-3 py-1.5 text-sm transition-all duration-200 border-l-2 -ml-px"
                                   :class="{
                                       'pl-6 text-zinc-500': h.level === 'h3',
                                       'pl-3 font-bold border-mint text-mint': activeId === h.id,
                                       'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-600': activeId !== h.id
                                   }"
                                   x-text="h.text"></a>
                            </template>
                        </nav>
                    </div>
                </template>
            </div>
        </aside>
    </div>

    <!-- Content Formatting Fix -->
    <style>
        /* Typography spacing */
        .prose {
            line-height: 1.8;
        }
        .prose p {
            margin-bottom: 1.5rem;
            text-align: justify;
        }
        .prose h2 {
            margin-top: 2.5rem;
            margin-bottom: 1rem;
            font-size: 1.75rem;
            font-weight: 700;
        }
        .prose h3 {
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            font-size: 1.375rem;
            font-weight: 600;
        }
        /* list styles handled globally in app.css */
        /* Media responsive */
        .prose img,
        .prose table,
        .prose pre,
        .prose iframe,
        .prose video {
            max-width: 100%;
            height: auto;
        }
        /* Code blocks */
        .prose pre {
            overflow-x: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
            padding: 1rem;
            background: #f4f4f5;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .dark .prose pre {
            background: #27272a;
        }
        .prose code {
            font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
            font-size: 0.875em;
        }
        .prose p code, .prose li code {
            background: #f4f4f5;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            color: #dc2626;
        }
        .dark .prose p code, .dark .prose li code {
            background: #27272a;
            color: #f87171;
        }
        /* Tables */
        .prose table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            margin-bottom: 1.5rem;
        }
    </style>

    <!-- Comments Section -->
    <section class="mb-16 pt-16 border-t border-zinc-100 dark:border-zinc-800" x-data="{ shown: false }" x-intersect.once.margin.-100px="shown = true">
        <div class="flex items-center justify-between mb-8 transition-all duration-1000 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <h3 class="text-2xl sm:text-3xl font-bold tracking-tight">Comments</h3>
            <span class="text-sm font-bold text-zinc-700 dark:text-zinc-200 bg-zinc-100 dark:bg-zinc-800 px-3 py-1 rounded-full">{{ $post->approvedComments->count() }}</span>
        </div>

        <!-- Comments List -->
        <div class="mb-8 transition-all duration-1000 delay-300 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <livewire:comment-list :blog-id="$post->id" />
        </div>

        <!-- Comment Form -->
        <livewire:comment-form :blog-id="$post->id" />
    </section>

    <!-- Author Section -->
    <div class="flex items-center gap-4 py-8 border-y border-zinc-100 dark:border-zinc-900 mb-16">
        <div class="w-14 h-14 rounded-full bg-mint flex items-center justify-center text-zinc-950 font-black text-lg">
            {{ substr($post->author ?? 'A', 0, 1) }}
        </div>
        <div>
            <p class="font-bold">{{ $post->author ?? 'Admin' }}</p>
            <p class="text-sm text-zinc-500">Author</p>
        </div>
    </div>

    <!-- Related Articles -->
    <div class="mb-12" x-data="{ shownRelated: false }" x-intersect.once.margin.-100px="shownRelated = true">
        <h3 class="text-2xl font-bold mb-8 transition-all duration-1000 transform" x-bind:class="shownRelated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">Related Articles</h3>

        <!-- Skeleton Loading -->
        <div wire:loading.delay class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @for($i = 0; $i < 2; $i++)
                <div class="animate-pulse">
                    <div class="aspect-[16/10] rounded-2xl bg-zinc-200 dark:bg-zinc-800 mb-4"></div>
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded w-1/4 mb-2"></div>
                    <div class="h-6 bg-zinc-200 dark:bg-zinc-800 rounded w-3/4"></div>
                </div>
            @endfor
        </div>

        <div wire:loading.remove class="grid grid-cols-1 md:grid-cols-2 gap-8 transition-all duration-1000 delay-300 transform" x-bind:class="shownRelated ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            @forelse($relatedPosts as $related)
                <a href="{{ route('blog.show', $related->slug) }}" wire:navigate class="group rounded-2xl border border-zinc-200/80 dark:border-zinc-800 p-4 sm:p-5 hover:border-mint/60 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-mint focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-950">
                    <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-4 bg-zinc-100 dark:bg-zinc-800">
                        @if($related->image || $related->image_url)
                            <x-optimized-image
                                :src="$related->image_url ?? Storage::url($related->image)"
                                :alt="$related->title"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            />
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
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="text-[10px] font-black uppercase tracking-widest text-mint">{{ $related->category }}</span>
                        <span class="text-zinc-300 dark:text-zinc-700">•</span>
                        <span class="text-xs text-zinc-600 dark:text-zinc-300">{{ $related->read_time }} min read</span>
                    </div>
                    <h4 class="text-lg font-bold group-hover:text-mint transition-colors">{{ $related->title }}</h4>
                </a>
            @empty
                <div class="md:col-span-2 rounded-2xl border border-dashed border-zinc-300 dark:border-zinc-700 bg-zinc-50/70 dark:bg-zinc-900/50 p-6 sm:p-8">
                    <p class="text-sm font-semibold text-zinc-700 dark:text-zinc-200">No related articles yet.</p>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Explore the journal for more topics and fresh updates.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-zinc-50 dark:bg-zinc-900 rounded-3xl p-6 sm:p-8 md:p-12 text-center">
            <h3 class="text-xl sm:text-2xl font-bold mb-4">Enjoyed this article?</h3>
            <p class="text-zinc-600 dark:text-zinc-300 mb-6 text-sm sm:text-base">Let's discuss your project and create something amazing together.</p>
            <a href="{{ route('contact.index') }}" wire:navigate class="inline-flex items-center gap-2 min-h-11 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-5 sm:px-6 py-2.5 sm:py-3 rounded-full font-bold hover:scale-105 transition-transform text-sm sm:text-base">
                Get in Touch
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="m12 5 7 7-7 7"/>
            </svg>
        </a>
    </div>
</main>
