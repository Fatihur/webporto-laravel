<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
<body class="bg-white text-zinc-950 dark:bg-zinc-950 dark:text-white transition-colors duration-300 min-h-screen">
    <!-- Simple Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 lg:px-12 py-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-2xl font-black tracking-tighter">ARTA</a>
            <a href="{{ route('home') }}" class="text-sm font-bold hover:text-mint transition-colors">Home</a>
        </div>
    </nav>

    <!-- 404 Content -->
    <main class="pt-32 pb-20 px-6 lg:px-12 min-h-screen flex items-center justify-center">
        <div class="text-center max-w-2xl mx-auto">
            <div class="mb-8">
                <span class="text-9xl md:text-[12rem] font-black tracking-tighter text-zinc-100 dark:text-zinc-800 leading-none">404</span>
            </div>

            <h1 class="text-3xl md:text-4xl font-bold mb-4">Page Not Found</h1>
            <p class="text-lg text-zinc-500 dark:text-zinc-400 mb-10 max-w-md mx-auto">
                The page you're looking for doesn't exist or has been moved.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
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
            </div>
        </div>
    </main>
</body>
</html>
