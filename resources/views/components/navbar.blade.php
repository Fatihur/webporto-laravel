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
                <svg class="w-8 h-8" viewBox="0 0 981 1032" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <style>
                        .nb-head-turn {
                            animation: nb-headTurn 10s infinite cubic-bezier(0.4, 0.0, 0.2, 1);
                            transform-origin: 490.5px 577.7px;
                        }
                        .nb-eye-look {
                            animation: nb-lookAround 10s infinite cubic-bezier(0.4, 0.0, 0.2, 1);
                        }
                        .nb-left-eye {
                            transform-origin: 385.5px 499px;
                            animation: nb-blink 5.5s infinite;
                        }
                        .nb-right-eye {
                            transform-origin: 595.5px 499px;
                            animation: nb-blink 5.5s infinite;
                        }
                        @keyframes nb-headTurn {
                            0%, 10% { transform: translate(0, 0) rotate(0deg); }
                            15%, 35% { transform: translate(-30px, 10px) rotate(-3deg); }
                            40%, 60% { transform: translate(40px, -10px) rotate(4deg); }
                            65%, 85% { transform: translate(0px, 20px) rotate(0deg); }
                            90%, 100% { transform: translate(0, 0) rotate(0deg); }
                        }
                        @keyframes nb-lookAround {
                            0%, 10% { transform: translate(0, 0); }
                            15%, 35% { transform: translate(-45px, 15px); }
                            40%, 60% { transform: translate(60px, -15px); }
                            65%, 85% { transform: translate(0px, 35px); }
                            90%, 100% { transform: translate(0, 0); }
                        }
                        @keyframes nb-blink {
                            0%, 46%, 49%, 53%, 100% { transform: scaleY(1); }
                            47.5%, 51.5% { transform: scaleY(0.05); }
                        }
                    </style>
                    <g class="nb-head-turn">
                        <path d="M923 577.713C923 817.247 729.363 889 490.5 889C251.637 889 58 817.247 58 577.713C58 338.18 251.637 144 490.5 144C729.363 144 923 338.18 923 577.713Z" fill="url(#nb-paint0_radial_1_2)"/>
                        <g class="nb-eye-look">
                            <g class="nb-left-eye">
                                <g filter="url(#nb-filter0_f_1_2)">
                                    <ellipse cx="385.5" cy="499" rx="76.5" ry="108" fill="white"/>
                                </g>
                            </g>
                            <g class="nb-right-eye">
                                <g filter="url(#nb-filter1_f_1_2)">
                                    <ellipse cx="595.5" cy="499" rx="76.5" ry="108" fill="white"/>
                                </g>
                            </g>
                        </g>
                    </g>
                    <defs>
                        <filter id="nb-filter0_f_1_2" x="305" y="387" width="161" height="224" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                            <feGaussianBlur stdDeviation="2" result="effect1_foregroundBlur_1_2"/>
                        </filter>
                        <filter id="nb-filter1_f_1_2" x="515" y="387" width="161" height="224" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                            <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                            <feGaussianBlur stdDeviation="2" result="effect1_foregroundBlur_1_2"/>
                        </filter>
                        <radialGradient id="nb-paint0_radial_1_2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(490.5 609.331) rotate(90) scale(279.669 324.716)">
                            <stop stop-color="#18E2AC"/>
                            <stop offset="1" stop-color="#82FFDE"/>
                        </radialGradient>
                    </defs>
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
            <svg class="w-7 h-7" viewBox="0 0 981 1032" fill="none" xmlns="http://www.w3.org/2000/svg">
                <style>
                    .nbm-head-turn {
                        animation: nbm-headTurn 10s infinite cubic-bezier(0.4, 0.0, 0.2, 1);
                        transform-origin: 490.5px 577.7px;
                    }
                    .nbm-eye-look {
                        animation: nbm-lookAround 10s infinite cubic-bezier(0.4, 0.0, 0.2, 1);
                    }
                    .nbm-left-eye {
                        transform-origin: 385.5px 499px;
                        animation: nbm-blink 5.5s infinite;
                    }
                    .nbm-right-eye {
                        transform-origin: 595.5px 499px;
                        animation: nbm-blink 5.5s infinite;
                    }
                    @keyframes nbm-headTurn {
                        0%, 10% { transform: translate(0, 0) rotate(0deg); }
                        15%, 35% { transform: translate(-30px, 10px) rotate(-3deg); }
                        40%, 60% { transform: translate(40px, -10px) rotate(4deg); }
                        65%, 85% { transform: translate(0px, 20px) rotate(0deg); }
                        90%, 100% { transform: translate(0, 0) rotate(0deg); }
                    }
                    @keyframes nbm-lookAround {
                        0%, 10% { transform: translate(0, 0); }
                        15%, 35% { transform: translate(-45px, 15px); }
                        40%, 60% { transform: translate(60px, -15px); }
                        65%, 85% { transform: translate(0px, 35px); }
                        90%, 100% { transform: translate(0, 0); }
                    }
                    @keyframes nbm-blink {
                        0%, 46%, 49%, 53%, 100% { transform: scaleY(1); }
                        47.5%, 51.5% { transform: scaleY(0.05); }
                    }
                </style>
                <g class="nbm-head-turn">
                    <path d="M923 577.713C923 817.247 729.363 889 490.5 889C251.637 889 58 817.247 58 577.713C58 338.18 251.637 144 490.5 144C729.363 144 923 338.18 923 577.713Z" fill="url(#nbm-paint0_radial_1_2)"/>
                    <g class="nbm-eye-look">
                        <g class="nbm-left-eye">
                            <g filter="url(#nbm-filter0_f_1_2)">
                                <ellipse cx="385.5" cy="499" rx="76.5" ry="108" fill="white"/>
                            </g>
                        </g>
                        <g class="nbm-right-eye">
                            <g filter="url(#nbm-filter1_f_1_2)">
                                <ellipse cx="595.5" cy="499" rx="76.5" ry="108" fill="white"/>
                            </g>
                        </g>
                    </g>
                </g>
                <defs>
                    <filter id="nbm-filter0_f_1_2" x="305" y="387" width="161" height="224" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                        <feGaussianBlur stdDeviation="2" result="effect1_foregroundBlur_1_2"/>
                    </filter>
                    <filter id="nbm-filter1_f_1_2" x="515" y="387" width="161" height="224" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                        <feGaussianBlur stdDeviation="2" result="effect1_foregroundBlur_1_2"/>
                    </filter>
                    <radialGradient id="nbm-paint0_radial_1_2" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(490.5 609.331) rotate(90) scale(279.669 324.716)">
                        <stop stop-color="#18E2AC"/>
                        <stop offset="1" stop-color="#82FFDE"/>
                    </radialGradient>
                </defs>
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

