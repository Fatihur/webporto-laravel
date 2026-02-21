<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <meta name="robots" content="noindex, nofollow">

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.svg') }}">
    <meta name="theme-color" content="#76D7A4">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @if (! app()->runningUnitTests())
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="bg-white text-zinc-950 dark:bg-zinc-950 dark:text-white transition-colors duration-300 min-h-screen overflow-x-hidden">
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 lg:px-12 py-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter">{{ config('app.name') }}</a>
            <a href="{{ route('home') }}" class="text-sm font-bold hover:text-mint transition-colors">Home</a>
        </div>
    </nav>

    <main class="relative pt-32 pb-20 px-6 lg:px-12 min-h-screen flex items-center justify-center">
        <div class="absolute -z-10 inset-0 pointer-events-none">
            <div class="absolute top-20 left-1/2 -translate-x-1/2 w-[30rem] h-[30rem] bg-mint/10 dark:bg-mint/5 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-10 right-10 w-64 h-64 bg-violet/10 dark:bg-violet/5 blur-[100px] rounded-full"></div>
        </div>

        <div class="text-center max-w-2xl mx-auto" x-data="{ show: false }" x-init="setTimeout(() => show = true, 80)">
            <div class="mb-8">
                <span class="text-9xl md:text-[12rem] font-black tracking-tighter text-zinc-100 dark:text-zinc-800 leading-none transition-all duration-700" x-bind:class="show ? 'opacity-100 scale-100' : 'opacity-0 scale-90'">404</span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold mb-4 transition-all duration-700 delay-100" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">Page Not Found</h1>
            <p class="text-lg text-zinc-500 dark:text-zinc-400 mb-10 max-w-md mx-auto transition-all duration-700 delay-200" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                The page you're looking for doesn't exist or has been moved.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center transition-all duration-700 delay-300" x-bind:class="show ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center gap-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 px-8 py-4 rounded-full font-bold hover:scale-105 transition-transform"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                    Back Home
                </a>

                <a href="{{ route('projects.category', 'graphic-design') }}"
                   class="inline-flex items-center justify-center gap-2 border border-zinc-200 dark:border-zinc-800 px-8 py-4 rounded-full font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors"
                >
                    View Projects
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                </a>

                <a href="{{ route('blog.index') }}"
                   class="inline-flex items-center justify-center gap-2 border border-zinc-200 dark:border-zinc-800 px-8 py-4 rounded-full font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors"
                >
                    Browse Blog
                </a>
            </div>
        </div>
    </main>
</body>
</html>
