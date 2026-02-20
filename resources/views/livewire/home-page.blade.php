<x-slot name="seo">
    <x-seo-meta title="Home" />
</x-slot>

<main class="px-6 lg:px-12 max-w-7xl mx-auto overflow-x-hidden">
    <!-- Hero Section -->
    <section class="relative h-[calc(100vh-5rem)] min-h-[700px] flex flex-col items-center justify-center text-center">
        <!-- Background Decorative Text -->
        <div class="absolute inset-0 -z-10 pointer-events-none select-none overflow-hidden flex items-center justify-center">
            <div class="text-[25vw] font-black text-zinc-100 dark:text-zinc-900/30 uppercase leading-none opacity-40 tracking-tighter whitespace-nowrap">
                FATIH
            </div>
        </div>

        <div class="relative z-10 w-full max-w-4xl px-4 flex flex-col items-center" x-data="{ show: false }" x-init="setTimeout(() => show = true, 100)">
            <p class="text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-[0.4em] text-[10px] sm:text-[11px] mb-6 sm:mb-8 transition-all duration-1000" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                / welcome to my Portfolio
            </p>

            <h1 class="text-6xl sm:text-8xl md:text-9xl font-extrabold tracking-tighter leading-none mb-6 transition-all duration-1000 delay-150" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                Hi, I'm <span class="text-mint">Fatih</span>
            </h1>

            <p class="text-lg sm:text-xl md:text-2xl font-semibold text-zinc-500 dark:text-zinc-400 mb-12 tracking-tight transition-all duration-1000 delay-300" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                Tech enthusiast
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto transition-all duration-1000 delay-500" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
                <button class="group w-full sm:min-w-[200px] bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-8 py-4 rounded-full font-bold hover:scale-105 active:scale-95 transition-all focus:outline-none focus:ring-2 focus:ring-mint focus:ring-offset-2 dark:focus:ring-offset-zinc-950 flex items-center justify-center gap-3">
                    Download CV
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                </button>
                <a
                    href="https://wa.me/6287758962661?text=Halo%20Fatih%2C%20saya%20tertarik%20dengan%20portfolio%20Anda.%20Boleh%20berdiskusi%3F"
                    target="_blank"
                    class="w-full sm:min-w-[200px] border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-950 text-zinc-950 dark:text-white px-8 py-4 rounded-full font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 hover:border-mint dark:hover:border-mint transition-all flex items-center justify-center gap-3"
                >
                    Let's Talk
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                </a>
            </div>
        </div>

        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 opacity-30 pointer-events-none animate-bounce">
            <span class="text-[9px] font-black uppercase tracking-[0.3em] text-zinc-400">Scroll</span>
            <div class="w-[1px] h-10 bg-gradient-to-b from-zinc-300 to-transparent dark:from-zinc-700"></div>
        </div>
    </section>

    <!-- Random Quote Section -->
    <livewire:random-quote />

    <!-- About Me Section -->
    <section id="about" class="mb-40" x-data="{ shown: false }" x-intersect.once.margin.-100px="shown = true">
        <div class="max-w-3xl mx-auto text-center transition-all duration-1000 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">

            <span class="text-mint font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">About Me</span>

            <h2 class="text-4xl sm:text-5xl font-extrabold tracking-tighter leading-tight mb-6">
                {{ $aboutMe['name'] }}
                <span class="block text-zinc-400 dark:text-zinc-600 text-xl sm:text-2xl mt-2 font-semibold">{{ $aboutMe['role'] }}</span>
            </h2>

            <p class="text-lg text-zinc-600 dark:text-zinc-400 leading-relaxed mb-10">
                {{ $aboutMe['bio'] }}
            </p>

            {{-- <!-- Simple Stats -->
            <div class="flex flex-wrap justify-center gap-8 mb-10">
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-mint">5+</div>
                    <div class="text-xs font-bold uppercase tracking-widest text-zinc-500 mt-1">Years Exp.</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-violet">50+</div>
                    <div class="text-xs font-bold uppercase tracking-widest text-zinc-500 mt-1">Projects</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-extrabold text-lime">20+</div>
                    <div class="text-xs font-bold uppercase tracking-widest text-zinc-500 mt-1">Clients</div>
                </div>
            </div> --}}

            <!-- Simple Social Links -->
            {{-- <div class="flex items-center justify-center gap-4">
                <a href="{{ $aboutMe['socials']['github'] }}" target="_blank" class="text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                </a>
                <a href="{{ $aboutMe['socials']['linkedin'] }}" target="_blank" class="text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                </a>
                <a href="{{ $aboutMe['socials']['twitter'] }}" target="_blank" class="text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </a>
                <a href="mailto:{{ $aboutMe['email'] }}" class="text-zinc-500 hover:text-zinc-950 dark:hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                </a>
            </div> --}}

        </div>
    </section>

    <!-- Expertise / Category Cards -->
    <section id="expertise" class="mb-40" x-data="{ shown: false }" x-intersect.once.margin.-100px="shown = true">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-6 transition-all duration-1000 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <div class="max-w-xl">
                <span class="text-mint font-bold uppercase tracking-[0.3em] text-[10px] mb-4 block">Core Competencies</span>
                <h2 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tighter leading-tight">Expertise</h2>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 transition-all duration-1000 delay-300 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            @foreach($categories as $index => $cat)
                <a
                    href="{{ route('projects.category', $cat['id']) }}"
                    wire:navigate
                    x-data="{ x: 0, y: 0 }"
                    @mousemove="x = $event.clientX - $el.getBoundingClientRect().left; y = $event.clientY - $el.getBoundingClientRect().top"
                    class="group relative overflow-hidden aspect-square rounded-[2.5rem] p-8 flex flex-col justify-between transition-all hover:scale-[1.02] active:scale-95 border border-transparent hover:border-mint/50 {{ $cat['color'] }}"
                >
                    <!-- Magic Glow Effect -->
                    <div class="pointer-events-none absolute -inset-px opacity-0 transition duration-300 group-hover:opacity-100"
                         :style="`background: radial-gradient(400px circle at ${x}px ${y}px, rgba(255,255,255,0.15), transparent 40%);`">
                    </div>
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
    <section class="mb-40 grid grid-cols-1 lg:grid-cols-12 gap-12 items-start" x-data="{ shown: false }" x-intersect.once.margin.-100px="shown = true">
        <div class="lg:col-span-5 transition-all duration-1000 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <h2 class="text-4xl font-extrabold mb-6">Experience</h2>
            <p class="text-zinc-500 dark:text-zinc-400 mb-8 leading-relaxed max-w-md">
                Focusing on the intersection of human-centered design and robust technical implementation for high-growth companies.
            </p>
            <div wire:loading.class="animate-pulse" class="flex flex-wrap gap-3">
                @foreach($tags as $tag)
                    <span class="px-4 py-2 bg-zinc-100 dark:bg-zinc-900 rounded-full text-[10px] font-bold uppercase tracking-widest">
                        {{ $tag }}
                    </span>
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-7 space-y-4 sm:space-y-6 transition-all duration-1000 delay-300 transform" x-bind:class="shown ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-12'">
            <!-- Skeleton Loading -->
            <div wire:loading.delay class="space-y-4">
                @for($i = 0; $i < 3; $i++)
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 sm:p-8 rounded-4xl border border-zinc-100 dark:border-zinc-800 gap-2">
                        <div class="space-y-2">
                            <div class="h-6 bg-zinc-200 dark:bg-zinc-700 rounded w-48 animate-pulse"></div>
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                        </div>
                        <div class="h-6 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                    </div>
                @endfor
            </div>

            <div wire:loading.remove>
            @foreach($experiences as $exp)
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-6 sm:p-8 rounded-4xl border border-zinc-100 dark:border-zinc-800 group hover:border-mint transition-colors gap-2">
                    <div>
                        <h4 class="text-lg sm:text-xl font-bold group-hover:text-mint transition-colors">{{ $exp->role }}</h4>
                        <p class="text-zinc-500 dark:text-zinc-400 text-sm">{{ $exp->company }}</p>
                    </div>
                    <span class="text-xs sm:text-sm font-bold opacity-60 bg-zinc-50 dark:bg-zinc-900 px-4 py-1 rounded-full">{{ $exp->dateRange() }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact CTA -->
    <section class="bg-zinc-950 dark:bg-zinc-900 rounded-[2.5rem] sm:rounded-[3rem] p-8 sm:p-12 md:p-24 text-center text-white relative overflow-hidden mb-20">
        <div class="relative z-10">
            <h2 class="text-3xl sm:text-4xl md:text-6xl font-extrabold mb-8 tracking-tighter leading-tight">
                Have a project in mind? <br class="hidden sm:block"> Let's create something <span class="text-lime italic">extraordinary</span>.
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
        <div class="absolute top-0 right-0 w-48 sm:w-64 h-48 sm:h-64 bg-mint/10 blur-[80px] sm:blur-[120px] rounded-full animate-blob"></div>
        <div class="absolute bottom-0 left-0 w-48 sm:w-64 h-48 sm:h-64 bg-violet/10 blur-[80px] sm:blur-[120px] rounded-full animate-blob-reverse"></div>
    </section>
</main>
