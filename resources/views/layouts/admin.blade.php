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
    <meta name="litespeed-cache-control" content="no-cache">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name') }}</title>

    <!-- Fonts -->
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

    @livewireStyles

    <!-- jQuery (required for Summernote) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

    <!-- MathJax for formulas -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [['$', '$'], ['\\(', '\\)']],
                displayMath: [['$$', '$$'], ['\\[', '\\]']]
            },
            svg: {
                fontCache: 'global'
            }
        };
    </script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

    <style>
        /* Summernote Dark Mode */
        .dark .note-editor {
            border-color: #27272a;
        }
        .dark .note-editor .note-toolbar {
            background: #18181b;
            border-color: #27272a;
        }
        .dark .note-editor .note-btn {
            background: #27272a;
            border-color: #3f3f46;
            color: #fff;
        }
        .dark .note-editor .note-btn:hover {
            background: #3f3f46;
        }
        .dark .note-editor .note-editing-area .note-editable {
            background: #09090b;
            color: #fff;
        }
        .dark .note-dropdown-menu {
            background: #18181b;
            border-color: #27272a;
        }
        .dark .note-dropdown-item {
            color: #fff;
        }
        .dark .note-dropdown-item:hover {
            background: #27272a;
        }
        /* Summernote Code Block styling */
        .note-editable pre {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 0.5rem;
            font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
        }
        .note-editable code {
            background: #f4f4f5;
            color: #dc2626;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            font-family: 'Fira Code', 'Consolas', 'Monaco', monospace;
            font-size: 0.875em;
        }
        .dark .note-editable code {
            background: #27272a;
            color: #f87171;
        }
        /* Summernote Fullscreen Mode */
        body.summernote-fullscreen {
            overflow: hidden;
        }
        .note-editor-fullscreen {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            z-index: 9999 !important;
            background: #fff !important;
        }
        /* Dark mode fullscreen */
        .note-editor-fullscreen.dark,
        html.dark .note-editor-fullscreen {
            background: #18181b !important;
        }
        .note-editor-fullscreen .note-toolbar {
            background: #f3f4f6 !important;
            border-bottom: 1px solid #e5e7eb !important;
        }
        .note-editor-fullscreen.dark .note-toolbar,
        html.dark .note-editor-fullscreen .note-toolbar {
            background: #27272a !important;
            border-bottom: 1px solid #3f3f46 !important;
        }
        .note-editor-fullscreen .note-editing-area {
            background: #fff !important;
        }
        .note-editor-fullscreen.dark .note-editing-area,
        html.dark .note-editor-fullscreen .note-editing-area {
            background: #09090b !important;
        }
        .note-editor-fullscreen .note-editable {
            background: #fff !important;
            color: #000 !important;
            min-height: calc(100vh - 100px) !important;
        }
        .note-editor-fullscreen.dark .note-editable,
        html.dark .note-editor-fullscreen .note-editable {
            background: #09090b !important;
            color: #fff !important;
        }
        .note-editor-fullscreen .note-statusbar {
            background: #f3f4f6 !important;
            border-top: 1px solid #e5e7eb !important;
        }
        .note-editor-fullscreen.dark .note-statusbar,
        html.dark .note-editor-fullscreen .note-statusbar {
            background: #27272a !important;
            border-top: 1px solid #3f3f46 !important;
        }
        /* Button styling in fullscreen */
        .note-editor-fullscreen .note-btn {
            background: #fff;
            border-color: #d1d5db;
        }
        .note-editor-fullscreen.dark .note-btn,
        html.dark .note-editor-fullscreen .note-btn {
            background: #27272a;
            border-color: #3f3f46;
            color: #fff;
        }
        .note-editor-fullscreen.dark .note-btn:hover,
        html.dark .note-editor-fullscreen .note-btn:hover {
            background: #3f3f46;
        }
        /* MathJax in editor */
        mjx-container {
            font-size: 1.1em !important;
        }
    </style>
</head>
<body class="bg-zinc-50 dark:bg-zinc-950 text-zinc-950 dark:text-white transition-colors duration-300">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('components.admin.sidebar')

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            @include('components.admin.header')

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto custom-scrollbar p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts

    <!-- MathJax re-render on Livewire updates -->
    <script>
        document.addEventListener('livewire:navigated', () => {
            if (typeof MathJax !== 'undefined') {
                MathJax.typesetPromise();
            }
        });
    </script>

    <!-- Redirect Handler -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('redirect-to-blog', (event) => {
                window.open(event.url, '_blank');
            });
        });
    </script>

    <!-- Notifications -->
    @include('components.admin.notifications')
</body>
</html>
