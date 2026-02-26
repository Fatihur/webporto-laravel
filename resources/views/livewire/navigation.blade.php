<nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
     x-data="{
         mobileMenuOpen: false,
         megaMenuOpen: false,
         searchOpen: false,
         isScrolled: false,
         currentPath: window.location.pathname,
         syncBodyScroll() {
             document.body.style.overflow = (this.mobileMenuOpen || this.searchOpen) ? 'hidden' : 'auto';
         },
         isActive(path) {
             if (path === '/') return this.currentPath === '/';
             return this.currentPath.startsWith(path);
         }
     }"
     x-init="
         document.addEventListener('livewire:navigated', () => { currentPath = window.location.pathname });
         window.addEventListener('scroll', () => { isScrolled = window.scrollY > 20 });
         isScrolled = window.scrollY > 20;
         $watch('mobileMenuOpen', () => syncBodyScroll());
         $watch('searchOpen', () => syncBodyScroll());
      "
     x-bind:class="isScrolled ? 'bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 shadow-sm' : 'bg-transparent border-transparent'"
     x-on:keydown.escape.window="mobileMenuOpen = false; megaMenuOpen = false; searchOpen = false"
     x-on:keydown.window="if ((event.ctrlKey || event.metaKey) && event.key === 'k') { event.preventDefault(); searchOpen = true; }"
     x-on:open-search.window="searchOpen = true"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3" x-on:click="megaMenuOpen = false">
                <svg width="36" height="38" viewBox="0 0 981 1032" fill="none" xmlns="http://www.w3.org/2000/svg" class="shrink-0">
                    <style>
                        .nl-head-turn {
                            animation: nl-headTurn 10s infinite cubic-bezier(0.4, 0.0, 0.2, 1);
                            transform-origin: 490.5px 577.7px;
                        }
                        .nl-eye-look {
                            animation: nl-lookAround 10s infinite cubic-bezier(0.4, 0.0, 0.2, 1);
                        }
                        .nl-left-eye {
                            transform-origin: 385.5px 499px;
                            animation: nl-blink 5.5s infinite;
                        }
                        .nl-right-eye {
                            transform-origin: 595.5px 499px;
                            animation: nl-blink 5.5s infinite;
                        }
                        @keyframes nl-headTurn {
                            0%, 10% { transform: translate(0, 0) rotate(0deg); }
                            15%, 35% { transform: translate(-30px, 10px) rotate(-3deg); }
                            40%, 60% { transform: translate(40px, -10px) rotate(4deg); }
                            65%, 85% { transform: translate(0px, 20px) rotate(0deg); }
                            90%, 100% { transform: translate(0, 0) rotate(0deg); }
                        }
                        @keyframes nl-lookAround {
                            0%, 10% { transform: translate(0, 0); }
                            15%, 35% { transform: translate(-45px, 15px); }
                            40%, 60% { transform: translate(60px, -15px); }
                            65%, 85% { transform: translate(0px, 35px); }
                            90%, 100% { transform: translate(0, 0); }
                        }
                        @keyframes nl-blink {
                            0%, 46%, 49%, 53%, 100% { transform: scaleY(1); }
                            47.5%, 51.5% { transform: scaleY(0.05); }
                        }
                    </style>
                    <g class="nl-head-turn">
                        <path d="M923 577.713C923 817.247 729.363 889 490.5 889C251.637 889 58 817.247 58 577.713C58 338.18 251.637 144 490.5 144C729.363 144 923 338.18 923 577.713Z" fill="url(#nl-paint0_radial_1_2)"/>
                        <g class="nl-eye-look">
                            <g class="nl-left-eye">
                                <g filter="url(#nl-filter0_f_1_2)">
                                    <ellipse cx="385.5" cy="499" rx="76.5" ry="108" fill="white"/>
                                </g>
                            </g>
                            <g class="nl-right-eye">
                                <g filter="url(#nl-filter1_f_1_2)">
                                    <ellipse cx="595.5" cy="499" rx="76.5" ry="108" fill="white"/>
                                </g>
                            </g>
                        </g>
                    </g>
                    <defs>
                        <filter id="nl-filter0_f_1_2" x="305" y="387" width="161" height="224" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                            <feGaussianBlur stdDeviation="2" result="effect1_foregroundBlur_1_2"/>
                        </filter>
                        <filter id="nl-filter1_f_1_2" x="515" y="387" width="161" height="224" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                            <feGaussianBlur stdDeviation="2" result="effect1_foregroundBlur_1_2"/>
                        </filter>
                        <radialGradient id="nl-paint0_radial_1_2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(490.5 609.331) rotate(90) scale(279.669 324.716)">
                            <stop stop-color="#18E2AC"/>
                            <stop offset="1" stop-color="#82FFDE"/>
                        </radialGradient>
                    </defs>
                </svg>
                <span class="font-black text-xl tracking-tight hidden sm:block">Fatih Porto</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors" x-bind:class="isActive('/') ? 'bg-mint/20 text-mint ring-1 ring-mint/50' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900'">Home</a>

                <!-- Projects Mega Menu -->
                <div class="relative">
                    <button
                        x-on:click="megaMenuOpen = !megaMenuOpen"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold transition-colors"
                        x-bind:class="isActive('/project') ? 'bg-mint/20 text-mint ring-1 ring-mint/50' : (megaMenuOpen ? 'bg-zinc-100 dark:bg-zinc-900' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900')"
                        x-bind:aria-expanded="megaMenuOpen"
                        aria-controls="projects-mega-menu"
                    >
                        Projects
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200" x-bind:class="megaMenuOpen ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div
                        id="projects-mega-menu"
                        x-show="megaMenuOpen"
                        x-on:click.outside="megaMenuOpen = false"
                        x-transition
                        class="fixed left-0 right-0 top-20 bg-white dark:bg-zinc-900 shadow-2xl border-b border-zinc-100 dark:border-zinc-800 p-6"
                        style="display: none;"
                    >
                        <div class="max-w-7xl mx-auto px-6 lg:px-12">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($categories as $category)
                                    <a href="{{ route('projects.category', $category['id']) }}" wire:navigate x-on:click="megaMenuOpen = false" class="group flex items-center gap-4 p-4 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                                        <div class="w-12 h-12 rounded-xl {{ $category['color'] }} flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm mb-1 group-hover:text-mint transition-colors">{{ $category['name'] }}</h4>
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1">{{ $category['description'] }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('blog.index') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors" x-bind:class="isActive('/blog') ? 'bg-mint/20 text-mint ring-1 ring-mint/50' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900'">Blog</a>
                <a href="{{ route('contact.index') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold transition-colors" x-bind:class="isActive('/contact') ? 'bg-mint/20 text-mint ring-1 ring-mint/50' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900'">Contact</a>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3">
                <!-- Search Button -->
                <button
                    type="button"
                    x-on:click="$dispatch('open-search')"
                    class="hidden md:flex items-center gap-2 px-3 h-10 rounded-full border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-mint focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-950"
                    aria-label="Search"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 group-hover:text-zinc-900 dark:text-zinc-400 dark:group-hover:text-zinc-100 transition-colors">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <span class="text-xs font-medium text-zinc-400 group-hover:text-zinc-600 dark:group-hover:text-zinc-300">Search</span>
                    <kbd class="hidden lg:inline-flex items-center gap-1 font-sans text-[10px] font-semibold px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                        <span class="text-xs">⌘</span>K
                    </kbd>
                </button>

                <!-- Divider -->
                <div class="hidden md:block w-px h-6 bg-zinc-200 dark:bg-zinc-800 mx-1"></div>

                <livewire:theme-toggle />
                <button type="button" x-on:click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden w-11 h-11 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-mint focus-visible:ring-offset-2 dark:focus-visible:ring-offset-zinc-950" x-bind:aria-expanded="mobileMenuOpen" aria-controls="mobile-nav-sidebar" aria-label="Toggle mobile menu">
                    <svg x-show="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    <svg x-show="mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <template x-teleport="body">
        <div x-show="mobileMenuOpen" style="display: none;">
            <!-- Overlay -->
            <div x-show="mobileMenuOpen" x-on:click="mobileMenuOpen = false" x-transition.opacity class="fixed inset-0 bg-black/50 z-[60] lg:hidden"></div>

            <!-- Sidebar -->
            <div id="mobile-nav-sidebar" x-show="mobileMenuOpen" x-transition:enter="transition transform ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 bottom-0 w-[min(22rem,100vw)] max-w-full bg-white dark:bg-zinc-950 z-[70] lg:hidden shadow-2xl flex flex-col" role="dialog" aria-modal="true" aria-label="Mobile navigation">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-zinc-100 dark:border-zinc-800 shrink-0">
                    <span class="font-black text-lg">Menu</span>
                    <button type="button" x-on:click="mobileMenuOpen = false" class="w-11 h-11 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-mint" aria-label="Close mobile menu">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Navigation -->
                    <div class="mb-8">
                        <p class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-4 px-2">Navigation</p>
                        <div class="space-y-1">
                            <a href="{{ route('home') }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-bold" x-bind:class="isActive('/') ? 'bg-mint/20 text-mint' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                Home
                            </a>
                            <a href="{{ route('blog.index') }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-bold" x-bind:class="isActive('/blog') ? 'bg-mint/20 text-mint' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                                Blog
                            </a>
                            <a href="{{ route('contact.index') }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors font-bold" x-bind:class="isActive('/contact') ? 'bg-mint/20 text-mint' : 'hover:bg-zinc-100 dark:hover:bg-zinc-900'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                Contact
                            </a>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-4 px-2">Categories</p>
                        <div class="space-y-1">
                            @foreach($categories as $category)
                                <a href="{{ route('projects.category', $category['id']) }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">
                                    <div class="w-8 h-8 rounded-lg {{ $category['color'] }} flex items-center justify-center shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7h10v10"/><path d="M7 17 17 7"/></svg>
                                    </div>
                                    <span class="font-medium text-sm">{{ $category['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- Search Modal -->
    <template x-teleport="body">
        <div
            x-show="searchOpen"
            style="display: none;"
            x-transition.opacity.duration.200ms
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[80] flex items-start justify-center pt-24 md:pt-32"
            x-on:keydown.escape.window="searchOpen = false"
            @close-search.window="searchOpen = false"
            role="dialog"
            aria-modal="true"
            aria-label="Search"
        >
            <div
                x-show="searchOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 -translate-y-4 scale-95"
                x-on:click.outside="searchOpen = false"
                class="w-full max-w-2xl mx-4"
            >
                <livewire:search-component />
            </div>
        </div>
    </template>
</nav>
