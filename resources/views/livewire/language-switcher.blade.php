<div class="relative inline-block">
    <button
        type="button"
        onclick="document.getElementById('lang-dropdown').classList.toggle('hidden')"
        class="flex items-center gap-1 sm:gap-2 px-2 sm:px-3 py-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors text-sm font-semibold border border-zinc-200 dark:border-zinc-700"
    >
        <span class="text-lg">{{ $locales[$currentLocale]['flag'] ?? '🌐' }}</span>
        <span class="hidden sm:inline text-xs">{{ $locales[$currentLocale]['native'] ?? 'Language' }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>

    <div
        id="lang-dropdown"
        class="hidden absolute right-0 top-full mt-2 w-48 bg-white dark:bg-zinc-900 rounded-2xl shadow-lg border border-zinc-100 dark:border-zinc-800 py-2 z-50"
    >
        @foreach($locales as $code => $info)
            <button
                wire:click="switchLocale('{{ $code }}')"
                class="w-full text-left px-4 py-3 flex items-center gap-3 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors {{ $currentLocale === $code ? 'text-mint font-semibold' : 'text-zinc-600 dark:text-zinc-300' }}"
            >
                <span class="text-lg">{{ $info['flag'] }}</span>
                <span>{{ $info['native'] }}</span>
                @if($currentLocale === $code)
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-auto w-4 h-4">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                @endif
            </button>
        @endforeach
    </div>
</div>
