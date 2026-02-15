<x-slot name="seo">
    <x-seo-meta title="Home" />
</x-slot>

<main class="px-6 lg:px-12 max-w-7xl mx-auto overflow-x-hidden">
    <!-- Hero Section - Uses Critical CSS classes -->
    <section class="hero-section">
        <!-- Background Decorative Text -->
        <div class="absolute inset-0 -z-10 pointer-events-none select-none overflow-hidden flex items-center justify-center" style="contain: paint;">
            <div style="font-size: 25vw; font-weight: 800; color: rgba(228,228,231,0.4); text-transform: uppercase; line-height: 1; letter-spacing: -0.05em; white-space: nowrap;" class="dark:text-zinc-900/30">
                FATIH
            </div>
        </div>

        <div class="relative z-10 w-full max-w-4xl flex flex-col items-center">
            <p class="hero-subtitle" style="color: #71717a;">
                / welcome to my Portfolio
            </p>

            <h1 class="hero-title">
                Hi, I'm <span class="text-mint">Fatih</span>
            </h1>

            <p class="hero-subtitle" style="margin-bottom: 3rem;">
                Tech enthusiast
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
                <button class="group w-full sm:min-w-[200px] bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-8 py-4 rounded-full font-bold hover:scale-105 transition-all flex items-center justify-center gap-3">
                    Download CV
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                </button>
                <a
                    href="https://wa.me/6287758962661?text=Halo%20Fatih%2C%20saya%20tertarik%20dengan%20portfolio%20Anda.%20Boleh%20berdiskusi%3F"
                    target="_blank"
                    class="w-full sm:min-w-[200px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-950 dark:text-white px-8 py-4 rounded-full font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all flex items-center justify-center gap-3"
                >
                    Let's Talk
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </a>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-30 pointer-events-none">
            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Scroll</span>
            <div class="w-[1px] h-10 bg-gradient-to-b from-zinc-300 to-transparent dark:from-zinc-700"></div>
        </div>
    </section>

    <!-- Random Quote Section -->
    <livewire:random-quote />

    <!-- About Me Section -->
    <section id="about" class="mb-40">
        <div class="max-w-3xl mx-auto text-center">

            <span class="text-mint font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">About Me</span>

            <h2 class="text-4xl sm:text-5xl font-extrabold tracking-tighter leading-tight mb-6">
                {{ $aboutMe['name'] }}
                <span class="block text-zinc-400 dark:text-zinc-600 text-xl sm:text-2xl mt-2 font-semibold">Tech Enthusiast</span>
            </h2>

            <p class="text-lg text-zinc-600 dark:text-zinc-400 leading-relaxed mb-10">
                I'm a passionate tech enthusiast with expertise in graphic design, software development, data analysis, and networking. With over 5 years of experience, I specialize in creating elegant solutions that bridge the gap between aesthetics and functionality.
            </p>

        </div>
    </section>

    <!-- Expertise / Category Cards -->
    <section id="expertise" class="mb-40">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6">
            <div class="max-w-xl">
                <span class="text-mint font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">Core Competencies</span>
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tighter leading-tight">Expertise</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($categories as $index => $cat)
                <a
                    href="{{ route('projects.category', $cat['id']) }}"
                    wire:navigate
                    class="group aspect-square rounded-[2.5rem] p-8 flex flex-col justify-between transition-all hover:scale-[1.02] active:scale-95 {{ $cat['color'] }}"
                >
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2 block">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <h3 class="text-2xl font-bold leading-tight">{{ $cat['name'] }}</h3>
                        <p class="text-sm mt-3 opacity-80 leading-relaxed font-medium">
                            {{ $cat['description'] }}
                        </p>
                    </div>
                    <div class="flex justify-end items-end">
                        <div class="w-12 h-12 bg-zinc-950/10 dark:bg-white/10 rounded-full flex items-center justify-center group-hover:bg-zinc-950 group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-zinc-950 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Key Stats Section -->
    <section class="mb-40">
        <livewire:stats-counter />
    </section>

    <!-- Experience Section -->
    <section class="mb-40 grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
        <div class="lg:col-span-5">
            <h2 class="text-4xl font-extrabold mb-6">Experience</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mb-8 leading-relaxed max-w-md">
                Focusing on the intersection of human-centered design and robust technical implementation for high-growth companies.
            </p>
            <div class="flex flex-wrap gap-3">
                @foreach($tags as $tagKey => $tag)
                    <span class="px-4 py-2 bg-zinc-100 dark:bg-zinc-900 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>
        <div class="lg:col-span-7 space-y-4 sm:space-y-6">
            @foreach($experiences as $exp)
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 sm:p-8 rounded-4xl border border-zinc-100 dark:border-zinc-800 group hover:border-mint transition-colors gap-2">
                    <div>
                        <h4 class="text-lg sm:text-xl font-bold group-hover:text-mint transition-colors">{{ $exp->role }}</h4>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm">{{ $exp->company }}</p>
                    </div>
                    <span class="text-xs sm:text-sm font-bold opacity-60 bg-zinc-50 dark:bg-zinc-900 px-4 py-1 rounded-full">
                        @if($exp->is_current)
                            {{ $exp->started_at?->format('M Y') }} - Present
                        @else
                            {{ $exp->started_at?->format('M Y') }} - {{ $exp->ended_at?->format('M Y') }}
                        @endif
                    </span>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="bg-zinc-950 dark:bg-zinc-900 rounded-[2.5rem] sm:rounded-[3rem] p-8 sm:p-12 md:p-24 text-center text-white relative overflow-hidden mb-20">
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-extrabold mb-8 tracking-tighter leading-tight">
                Have a project in mind? Let's create something extraordinary.
            </h2>
            <a
                href="{{ route('contact.index') }}"
                wire:navigate
                class="inline-flex items-center gap-3 bg-white text-zinc-950 px-8 sm:px-10 py-4 sm:py-5 rounded-full font-black text-base sm:text-lg hover:scale-105 transition-transform"
            >
                Start Project
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
        <div class="absolute top-0 right-0 w-48 sm:w-64 h-48 sm:h-64 bg-mint/10 blur-[80px] sm:blur-[120px] rounded-full"></div>
        <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-violet/10 blur-[80px] sm:blur-[120px] rounded-full"></div>
    </section>
</main>
