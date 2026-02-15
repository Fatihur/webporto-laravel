<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{ $seo ?? '' }}

    <!-- Critical Resource Hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Preload Critical Font -->
    <link rel="preload" href="https://fonts.gstatic.com/s/inter/v18/UcCo3FwrK3iLTcviYwY.woff2" as="font" type="font/woff2" crossorigin>

    <!-- Critical CSS Inline for Above-the-Fold Content -->
    <style>
        /* Critical CSS - Above the fold content */
        *,*::before,*::after{box-sizing:border-box}
        html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
        body{margin:0;font-family:Inter,system-ui,-apple-system,sans-serif;background:#fff;color:#09090b;line-height:1.5}
        .dark body{background:#09090b;color:#fff}

        /* Navbar Critical Styles */
        nav{position:fixed;top:0;left:0;width:100%;z-index:50;background:rgba(255,255,255,.8);backdrop-filter:blur(12px);border-bottom:1px solid #f4f4f5}
        .dark nav{background:rgba(9,9,11,.8);border-color:#27272a}

        /* Hero Critical Styles */
        .hero-section{position:relative;height:calc(100vh - 5rem);min-height:700px;display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:0 1.5rem}
        .hero-title{font-size:clamp(3rem,15vw,9rem);font-weight:800;letter-spacing:-.05em;line-height:1;margin:0 0 1.5rem}
        .hero-subtitle{font-size:clamp(1.125rem,2.5vw,1.5rem);font-weight:600;color:#71717a;margin:0 0 3rem}
        .text-mint{color:#76D7A4}

        /* Layout Critical */
        .max-w-7xl{max-width:80rem;margin:0 auto;padding:0 1.5rem}

        /* Reduced Motion */
        @media (prefers-reduced-motion:reduce){html{scroll-behavior:auto}*{animation-duration:.01ms!important;animation-iteration-count:1!important;transition-duration:.01ms!important}}

        /* Font Face - Inter Regular */
        @font-face{font-family:Inter;font-style:normal;font-weight:400;font-display:swap;src:url(https://fonts.gstatic.com/s/inter/v18/UcCo3FwrK3iLTcviYwY.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
        @font-face{font-family:Inter;font-style:normal;font-weight:700;font-display:swap;src:url(https://fonts.gstatic.com/s/inter/v18/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuFuYAZ9hjp-Ek-_EeA.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
        @font-face{font-family:Inter;font-style:normal;font-weight:800;font-display:swap;src:url(https://fonts.gstatic.com/s/inter/v18/UcC73FwrK3iLTeHuS_nVMrMxCp50SjIw2boKoduKmMEVuDyYAZ9hjp-Ek-_EeA.woff2) format('woff2');unicode-range:U+0000-00FF,U+0131,U+0152-0153,U+02BB-02BC,U+02C6,U+02DA,U+02DC,U+0304,U+0308,U+0329,U+2000-206F,U+2074,U+20AC,U+2122,U+2191,U+2193,U+2212,U+2215,U+FEFF,U+FFFD}
    </style>

    <!-- Async Load Non-Critical CSS -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"></noscript>

    <!-- Tailwind CSS - Async Load with fallback -->
    <link rel="preload" href="https://cdn.tailwindcss.com" as="script">
    <script src="https://cdn.tailwindcss.com" defer></script>
    <script>
        window.tailwindConfig = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        mint: '#76D7A4',
                        violet: '#C4A1FF',
                        lime: '#E8FF8E',
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '2.5rem',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        };
        // Apply config when Tailwind loads
        if (window.tailwind) {
            tailwind.config = window.tailwindConfig;
        }
    </script>

    <!-- Preconnect to storage for images -->
    <link rel="preconnect" href="{{ config('filesystems.disks.public.url', '') }}" crossorigin>

    @livewireStyles

    <!-- Non-Critical Styles - Lazy Loaded -->
    <style>
        /* Custom scrollbar - non critical */
        .custom-scrollbar::-webkit-scrollbar{width:6px}
        .custom-scrollbar::-webkit-scrollbar-track{background:transparent}
        .custom-scrollbar::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:10px}
        .dark .custom-scrollbar::-webkit-scrollbar-thumb{background:#334155}

        /* Animation classes - loaded after initial paint */
        @media (prefers-reduced-motion:no-preference) {
            .animate-in{animation:animateIn .3s ease-out forwards}
            @keyframes animateIn{from{opacity:0}to{opacity:1}}
        }

        /* Code block styling - only needed for blog posts */
        .prose pre{background:#1e1e1e;color:#d4d4d4;padding:1rem;border-radius:.75rem;overflow-x:auto;font-family:'Fira Code','Consolas',monospace;font-size:.875rem}
        .prose pre code{background:transparent;color:inherit;padding:0;font-size:inherit}
    </style>

    <!-- MathJax - Only load on pages that need it (blog posts with formulas) -->
    @hasSection('mathjax')
    <script>
        window.MathJax = {
            tex: {inlineMath:[['$','$'],['\\(','\\)']],displayMath:[['$$','$$'],['\\[','\\]']]},
            svg: {fontCache:'global'},
            startup: {pageReady:()=>MathJax.startup.defaultPageReady()}
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    @endif
</head>
<body class="bg-white text-zinc-950 dark:bg-zinc-950 dark:text-white min-h-screen antialiased">
    <livewire:navigation />

    <main>
        {{ $slot }}
    </main>

    @include('components.footer')

    <!-- Critical Theme Script - Inline for instant execution -->
    <script>
        (function() {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    <!-- Livewire Scripts - Defer loading -->
    @livewireScripts

    <!-- Deferred Scripts -->
    <script defer>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('theme-changed', (data) => {
                document.documentElement.classList.toggle('dark', data.theme === 'dark');
                localStorage.setItem('theme', data.theme);
            });
        });

        // MathJax re-render on navigation - only if MathJax is loaded
        document.addEventListener('livewire:navigated', () => {
            if (typeof MathJax !== 'undefined' && MathJax.typesetPromise) {
                MathJax.typesetPromise();
            }
        });
    </script>
</body>
</html>
