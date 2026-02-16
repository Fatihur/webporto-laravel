<nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-100 dark:border-zinc-800"
    x-data="{ mobileMenuOpen: false, megaMenuOpen: false, searchOpen: false }"
    x-on:keydown.escape.window="mobileMenuOpen = false; megaMenuOpen = false; searchOpen = false"
    x-on:open-search.window="searchOpen = true"
>
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3" x-on:click="megaMenuOpen = false">
                <div class="w-10 h-10 rounded-full bg-mint flex items-center justify-center text-zinc-950 font-black text-lg">A</div>
                <span class="font-black text-xl tracking-tight hidden sm:block">ArtaPortfolio</span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">Home</a>

                <!-- Projects Mega Menu -->
                <div class="relative">
                    <button
                        x-on:click="megaMenuOpen = !megaMenuOpen"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-full text-sm font-bold hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors"
                        x-bind:class="megaMenuOpen ? 'bg-zinc-100 dark:bg-zinc-900' : ''"
                    >
                        Projects
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="transition-transform duration-200" x-bind:class="megaMenuOpen ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div
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

                <a href="{{ route('blog.index') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">Blog</a>
                <a href="{{ route('contact.index') }}" wire:navigate class="px-5 py-2.5 rounded-full text-sm font-bold hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">Contact</a>
            </div>

            <!-- Right Side -->
            <div class="flex items-center gap-3">
                <!-- Search Button -->
                <button
                    type="button"
                    x-on:click="$dispatch('open-search')"
                    class="hidden md:flex w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-800 items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors"
                    aria-label="Search"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                </button>

                <livewire:theme-toggle />
                <button type="button" x-on:click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">
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
            <div x-show="mobileMenuOpen" x-transition:enter="transition transform ease-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition transform ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="fixed top-0 right-0 bottom-0 w-80 max-w-full bg-white dark:bg-zinc-950 z-[70] lg:hidden shadow-2xl flex flex-col">
                <!-- Header -->
                <div class="flex items-center justify-between p-6 border-b border-zinc-100 dark:border-zinc-800 shrink-0">
                    <span class="font-black text-lg">Menu</span>
                    <button type="button" x-on:click="mobileMenuOpen = false" class="w-10 h-10 rounded-full border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    <!-- Navigation -->
                    <div class="mb-8">
                        <p class="text-xs font-black uppercase tracking-widest text-zinc-400 mb-4 px-2">Navigation</p>
                        <div class="space-y-1">
                            <a href="{{ route('home') }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                                Home
                            </a>
                            <a href="{{ route('blog.index') }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors font-bold">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                                Blog
                            </a>
                            <a href="{{ route('contact.index') }}" wire:navigate x-on:click="mobileMenuOpen = false" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors font-bold">
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
