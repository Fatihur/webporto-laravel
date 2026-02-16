<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#76D7A4">

    {{ $seo ?? '' }}

    <!-- Resource Hints for External Assets -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <!-- Fonts with display=swap for better perceived performance -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        mint: '#76D7A4',
                        violet: '#C4A1FF',
                        lime: '#E8FF8E',
                        zinc: {
                            950: '#09090b',
                        }
                    },
                    borderRadius: {
                        '4xl': '2rem',
                        '5xl': '2.5rem',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

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
    </style>

    @livewireStyles

    <!-- MathJax for formula rendering -->
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

    <!-- AI Chat Bot -->
    <livewire:chat-bot />

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

    @stack('scripts')
</body>
</html>
