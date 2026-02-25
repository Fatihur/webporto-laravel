@php
$navLinks = App\Data\CategoryData::getNavLinks();
$categories = App\Data\CategoryData::all();
$currentRoute = Route::currentRouteName();
$isProjectsActive = request()->routeIs('projects.*');
@endphp

<!-- Main Navbar -->
<nav class="fixed top-0 left-0 w-full z-50 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-100 dark:border-zinc-800 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-6 lg:px-12">
        <div class="flex justify-between items-center h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-8 h-8">
                    <rect width="100" height="100" rx="20" fill="#111827" class="dark:hidden"/>
                    <rect width="100" height="100" rx="20" fill="white" class="hidden dark:block"/>
                    <g stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="dark:hidden">
                        <circle cx="32" cy="48" r="18"/>
                        <circle cx="68" cy="48" r="18"/>
                        <path d="M50 48 H50"/>
                        <path d="M10 48 H14"/>
                        <path d="M90 48 H86"/>
                        <path d="M26 32 L22 26"/>
                        <path d="M74 32 L78 26"/>
                    </g>
                    <g stroke="#111827" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="hidden dark:block">
                        <circle cx="32" cy="48" r="18"/>
                        <circle cx="68" cy="48" r="18"/>
                        <path d="M50 48 H50"/>
                        <path d="M10 48 H14"/>
                        <path d="M90 48 H86"/>
                        <path d="M26 32 L22 26"/>
                        <path d="M74 32 L78 26"/>
                    </g>
                </svg>
                <span class="text-xl font-extrabold tracking-tighter">Fatih</span>
            </a>

            <!-- Desktop Links -->
            <div class="hidden md:flex items-center space-x-2">
                @foreach($navLinks as $link)
                    @if($link['name'] === 'Projects')
                        <div class="relative group" id="projects-menu">
                                @php
                                $projectsClasses = $isProjectsActive
                                    ? 'bg-mint/20 text-mint ring-1 ring-mint/50'
                                    : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-mint';
                            @endphp
                            <button
                                onclick="toggleMegaMenu()"
                                class="flex items-center gap-1 px-4 py-2 rounded-full text-sm font-semibold transition-all {{ $projectsClasses }}"
                            >
                                {{ $link['name'] }}
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" id="mega-menu-chevron"></i>
                            </button>
                        </div>
                    @else
                        @php
                            $isActive = $currentRoute === $link['route'];
                            $linkClasses = $isActive
                                ? 'bg-mint/20 text-mint ring-1 ring-mint/50'
                                : 'text-zinc-600 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-mint';
                        @endphp
                        <a
                            href="{{ $link['path'] }}"
                            class="px-4 py-2 rounded-full text-sm font-semibold transition-all {{ $linkClasses }}"
                        >
                            {{ $link['name'] }}
                        </a>
                    @endif
                @endforeach
            </div>

            <!-- Actions -->
            <div class="flex items-center space-x-4">
                <button
                    onclick="toggleTheme()"
                    class="p-2.5 rounded-full bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-mint"
                    aria-label="Toggle Theme"
                >
                    <i data-lucide="moon" class="w-5 h-5" id="theme-icon"></i>
                </button>
                <a
                    href="{{ route('contact.index') }}"
                    class="hidden sm:block bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-6 py-2.5 rounded-full text-sm font-bold hover:scale-105 transition-transform"
                >
                    Get Started
                </a>
                <button
                    onclick="openMobileMenu()"
                    class="md:hidden p-2 text-zinc-600 dark:text-zinc-300"
                    aria-label="Open Menu"
                >
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mega Menu Dropdown (Desktop) -->
    <div
        id="mega-menu"
        class="hidden absolute top-20 left-0 w-full bg-white dark:bg-zinc-950 border-b border-zinc-100 dark:border-zinc-800 shadow-xl overflow-hidden"
        onmouseleave="closeMegaMenu()"
    >
        <div class="max-w-7xl mx-auto px-6 lg:px-12 py-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($categories as $cat)
                <a
                    href="{{ route('projects.category', $cat['id']) }}"
                    onclick="closeMegaMenu()"
                    class="group p-6 rounded-3xl border border-zinc-100 dark:border-zinc-800 hover:border-mint dark:hover:border-mint hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-all"
                >
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 {{ $cat['color'] }} group-hover:scale-110 transition-transform">
                        @if($cat['icon'] === 'palette')
                            <i data-lucide="palette" class="w-5 h-5"></i>
                        @elseif($cat['icon'] === 'code')
                            <i data-lucide="code-2" class="w-5 h-5"></i>
                        @elseif($cat['icon'] === 'chart')
                            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        @elseif($cat['icon'] === 'network')
                            <i data-lucide="network" class="w-5 h-5"></i>
                        @endif
                    </div>
                    <h3 class="text-lg font-bold mb-1 group-hover:text-mint transition-colors">{{ $cat['name'] }}</h3>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                        {{ $cat['description'] }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay -->
<div
    id="mobile-menu-overlay"
    class="fixed inset-0 z-[60] bg-zinc-950/40 backdrop-blur-sm transition-opacity duration-300 md:hidden opacity-0 pointer-events-none"
    onclick="closeMobileMenu()"></div>

<!-- Mobile Sidebar -->
<aside
    id="mobile-sidebar"
    class="fixed top-0 right-0 z-[70] w-[85%] max-w-sm h-full bg-white dark:bg-zinc-950 border-l border-zinc-100 dark:border-zinc-800 transition-transform duration-500 ease-out md:hidden flex flex-col translate-x-full"
>
    <div class="flex items-center justify-between p-6 border-b border-zinc-50 dark:border-zinc-900">
        <a href="{{ route('home') }}" class="flex items-center gap-2" onclick="closeMobileMenu()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" class="w-7 h-7">
                <rect width="100" height="100" rx="20" fill="#111827" class="dark:hidden"/>
                <rect width="100" height="100" rx="20" fill="white" class="hidden dark:block"/>
                <g stroke="white" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="dark:hidden">
                    <circle cx="32" cy="48" r="18"/>
                    <circle cx="68" cy="48" r="18"/>
                    <path d="M50 48 H50"/>
                    <path d="M10 48 H14"/>
                    <path d="M90 48 H86"/>
                    <path d="M26 32 L22 26"/>
                    <path d="M74 32 L78 26"/>
                </g>
                <g stroke="#111827" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="hidden dark:block">
                    <circle cx="32" cy="48" r="18"/>
                    <circle cx="68" cy="48" r="18"/>
                    <path d="M50 48 H50"/>
                    <path d="M10 48 H14"/>
                    <path d="M90 48 H86"/>
                    <path d="M26 32 L22 26"/>
                    <path d="M74 32 L78 26"/>
                </g>
            </svg>
            <span class="text-lg font-black tracking-tighter">Fatih</span>
        </a>
        <button
            onclick="closeMobileMenu()"
            class="p-2 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors focus:outline-none focus:ring-2 focus:ring-mint"
        >
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
        <div class="flex flex-col space-y-8">
            <div class="space-y-2">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-4">Navigation</p>
                @foreach($navLinks as $link)
                    @if($link['name'] === 'Projects')
                        @php
                            $mobileProjectsClasses = $isProjectsActive
                                ? 'bg-mint/20 text-mint border-l-4 border-mint'
                                : 'text-zinc-950 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900 border-l-4 border-transparent';
                        @endphp
                        <a
                            href="{{ $link['path'] }}"
                            onclick="closeMobileMenu()"
                            class="flex items-center gap-3 px-4 py-3 rounded-2xl text-2xl font-bold tracking-tighter transition-all {{ $mobileProjectsClasses }}"
                        >
                            {{ $link['name'] }}
                            @if($isProjectsActive)
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="ml-auto">
                                    <path d="m5 12 7-7 7 7"/>
                                </svg>
                            @endif
                        </a>
                    @else
                        @php
                            $isMobileActive = $currentRoute === $link['route'];
                            $mobileLinkClasses = $isMobileActive
                                ? 'bg-mint/20 text-mint border-l-4 border-mint'
                                : 'text-zinc-950 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900 border-l-4 border-transparent';
                        @endphp
                        <a
                            href="{{ $link['path'] }}"
                            onclick="closeMobileMenu()"
                            class="flex items-center gap-3 px-4 py-3 rounded-2xl text-2xl font-bold tracking-tighter transition-all {{ $mobileLinkClasses }}"
                        >
                            {{ $link['name'] }}
                            @if($isMobileActive)
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="ml-auto">
                                    <path d="m5 12 7-7 7 7"/>
                                </svg>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="space-y-4 pt-8 border-t border-zinc-50 dark:border-zinc-900">
                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-400 mb-4">Categories</p>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($categories as $cat)
                        <a
                            href="{{ route('projects.category', $cat['id']) }}"
                            onclick="closeMobileMenu()"
                            class="flex items-center justify-between p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 group active:scale-95 transition-all"
                        >
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $cat['color'] }} opacity-80 group-hover:opacity-100 transition-opacity">
                                    @if($cat['icon'] === 'palette')
                                        <i data-lucide="palette" class="w-4 h-4"></i>
                                    @elseif($cat['icon'] === 'code')
                                        <i data-lucide="code-2" class="w-4 h-4"></i>
                                    @elseif($cat['icon'] === 'chart')
                                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                                    @elseif($cat['icon'] === 'network')
                                        <i data-lucide="network" class="w-4 h-4"></i>
                                    @endif
                                </div>
                                <span class="font-bold text-sm">{{ $cat['name'] }}</span>
                            </div>
                            <i data-lucide="arrow-right" class="w-4 h-4 text-zinc-400 group-hover:text-mint group-hover:translate-x-1 transition-all"></i>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 border-t border-zinc-50 dark:border-zinc-900">
        <a
            href="{{ route('contact.index') }}"
            onclick="closeMobileMenu()"
            class="w-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 py-4 rounded-2xl font-black text-center block"
        >
            Work with me
        </a>
    </div>
</aside>

