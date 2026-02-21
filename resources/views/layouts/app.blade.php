<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#76D7A4">

    <!-- View Transitions API -->
    <meta name="view-transition" content="same-origin">

    @if (isset($seo) && filled($seo))
        {{ $seo }}
    @else
        <x-seo-meta />
    @endif

    <x-structured-data :data="app(\App\Services\SeoService::class)->generateWebsiteStructuredData()" />
    <x-structured-data :data="app(\App\Services\SeoService::class)->generatePersonStructuredData()" />

    <!-- Resource Hints for External Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <!-- Fonts with display=swap for better perceived performance -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if (! app()->runningUnitTests())
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

        /* Animation classes */
        .animate-in {
            animation: animateIn 0.3s ease-out forwards;
        }
        .slide-in-from-top-4 {
            animation: slideInFromTop 0.3s ease-out forwards;
        }
        .slide-in-from-right {
            animation: slideInFromRight 0.3s ease-out forwards;
        }
        @keyframes animateIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideInFromTop {
            from {
                opacity: 0;
                transform: translateY(-1rem);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes slideInFromRight {
            from {
                opacity: 0;
                transform: translateX(100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Smooth transitions */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Livewire specific */
        [wire\:loading] {
            display: none;
        }
        [wire\:loading\.block] {
            display: block;
        }
        [wire\:loading\.inline] {
            display: inline;
        }
        [wire\:loading\.flex] {
            display: flex;
        }

        /* View Transitions fallback rules */
        @supports (view-transition-name: root) {
            ::view-transition-old(root),
            ::view-transition-new(root) {
                animation-duration: 0.4s;
            }
            ::view-transition-old(root) {
                animation-name: fadeOut;
            }
            ::view-transition-new(root) {
                animation-name: fadeIn;
            }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>

    @livewireStyles

    @if (!empty($enableMathJax))
        <!-- MathJax for formula rendering (loaded only on pages that need it) -->
        <script>
            window.MathJax = {
                tex: {
                    inlineMath: [['$', '$'], ['\\(', '\\)']],
                    displayMath: [['$$', '$$'], ['\\[', '\\]']]
                },
                svg: {
                    fontCache: 'global'
                },
                startup: {
                    pageReady: () => {
                        return MathJax.startup.defaultPageReady();
                    }
                }
            };
        </script>
        <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    @endif

    <!-- Code block styling -->
    <style>
        /* CKEditor code block styling */
        .prose pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 0.75rem;
            overflow-x: auto;
            font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .prose pre code {
            background: transparent;
            color: inherit;
            padding: 0;
            font-size: inherit;
        }
        .prose code {
            background: #f4f4f5;
            color: #dc2626;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
        }
        .dark .prose code {
            background: #27272a;
            color: #f87171;
        }
        /* MathJax formula styling */
        mjx-container {
            font-size: 1.1em !important;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-white text-zinc-950 dark:bg-zinc-950 dark:text-white transition-colors duration-300 min-h-screen">
    <livewire:navigation />

    <main>
        {{ $slot }}
    </main>

    @include('components.footer')

    <!-- AI Chat Widget -->
    <livewire:ai-chat-widget />

    <!-- Global Toast Notifications -->
    <div x-data="{ toasts: [] }" 
         @notify.window="
            let id = Date.now();
            toasts.push({ id: id, message: $event.detail.message, type: $event.detail.type || 'info' });
            setTimeout(() => { toasts = toasts.filter(t => t.id !== id) }, 3000);
         "
         class="fixed bottom-24 right-4 sm:bottom-4 sm:right-4 z-[100] flex flex-col gap-2 pointer-events-none"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:translate-x-4"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="px-4 py-3 rounded-xl shadow-lg border flex items-center gap-3 w-72 pointer-events-auto backdrop-blur-md"
                 :class="{
                    'bg-mint/10 dark:bg-mint/10 border-mint/20 dark:border-mint/20 text-mint': toast.type === 'success',
                    'bg-red-500/10 dark:bg-red-500/10 border-red-500/20 dark:border-red-500/20 text-red-500': toast.type === 'error',
                    'bg-white/80 dark:bg-zinc-900/80 border-zinc-200 dark:border-zinc-800 text-zinc-900 dark:text-zinc-100': toast.type === 'info'
                 }">
                 <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0"
                      :class="{
                         'bg-mint/20 text-mint': toast.type === 'success',
                         'bg-red-500/20 text-red-500': toast.type === 'error',
                         'bg-zinc-100 dark:bg-zinc-800 text-zinc-500': toast.type === 'info'
                      }">
                      <template x-if="toast.type === 'success'">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                      </template>
                      <template x-if="toast.type === 'error'">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                      </template>
                      <template x-if="toast.type === 'info'">
                          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                      </template>
                 </div>
                 <div class="flex-1 text-sm font-semibold" x-text="toast.message"></div>
                 <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                     <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                 </button>
            </div>
        </template>
    </div>

    <!-- Global Image Lightbox -->
    <div x-data="{ 
            open: false, 
            imgSrc: '', 
            init() {
                window.addEventListener('open-lightbox', (e) => {
                    this.imgSrc = e.detail;
                    this.open = true;
                    document.body.style.overflow = 'hidden';
                });
                this.$watch('open', value => {
                    if(!value) document.body.style.overflow = 'auto';
                });
            }
         }"
         x-show="open"
         style="display: none;"
         class="fixed inset-0 z-[110] bg-zinc-950/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-8"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
    >
        <button @click="open = false" class="absolute top-4 right-4 sm:top-8 sm:right-8 w-12 h-12 bg-white/10 hover:bg-white/20 rounded-full flex items-center justify-center text-white transition-colors">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <img :src="imgSrc" @click.outside="open = false" class="max-w-full max-h-full rounded-2xl shadow-2xl object-contain" 
             x-show="open"
             x-transition:enter="transition ease-out duration-300 delay-100"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
        >
    </div>

    <!-- Theme handling script -->
    <script>
        // Initialize theme from session/localStorage on page load
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();

        // Listen for Livewire theme changes
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('theme-changed', (data) => {
                const isDark = data.theme === 'dark';
                if (isDark) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                localStorage.setItem('theme', data.theme);
            });
        });
    </script>

    @livewireScripts

    @if (!empty($enableMathJax))
        <!-- MathJax re-render on Livewire navigation -->
        <script>
            document.addEventListener('livewire:navigated', () => {
                if (typeof MathJax !== 'undefined' && MathJax.startup && MathJax.startup.promise) {
                    MathJax.startup.promise.then(() => {
                        MathJax.typesetPromise();
                    }).catch((err) => {
                        console.log('MathJax typeset error:', err);
                    });
                }
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>
