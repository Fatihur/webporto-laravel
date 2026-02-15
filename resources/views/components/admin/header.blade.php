<header class="h-16 bg-white/80 dark:bg-zinc-900/80 backdrop-blur-sm border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between px-6 lg:px-8">
    <div class="flex items-center gap-4 lg:hidden">
        <span class="text-lg font-bold">Admin</span>
    </div>

    <div class="hidden lg:flex items-center gap-4">
        <h2 class="text-lg font-bold">@yield('page-title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center gap-4">
        {{-- Dark Mode Toggle --}}
        <livewire:theme-toggle />

        {{-- User Menu --}}
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-mint flex items-center justify-center text-zinc-950 font-bold text-sm">
                {{ substr(auth()->user()?->name ?? 'A', 0, 1) }}
            </div>
            <span class="hidden sm:block text-sm font-medium">{{ auth()->user()?->name ?? 'Admin' }}</span>
        </div>
    </div>
</header>
