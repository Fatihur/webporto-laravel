<div class="w-full">
    @if ($success)
        <div x-data x-init="setTimeout(() => $wire.success = false, 5000)"
             class="p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 rounded-xl text-sm">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                Thank you for subscribing! Check your email for confirmation.
            </div>
        </div>
    @else
        <form wire:submit="subscribe" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input
                    type="email"
                    wire:model="email"
                    placeholder="Enter your email"
                    class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-0 rounded-xl text-sm focus:ring-2 focus:ring-mint"
                    required
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold text-sm rounded-xl hover:scale-105 transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span wire:loading.remove>Subscribe</span>
                <span wire:loading>Subscribing...</span>
            </button>
        </form>
    @endif
</div>
