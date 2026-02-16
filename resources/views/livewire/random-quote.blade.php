<section class="py-16 border-y border-zinc-100 dark:border-zinc-900 mb-32 relative overflow-hidden">
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <!-- Quote Icon -->
        <div class="mb-6 flex justify-center">
            <div class="w-12 h-12 rounded-full bg-mint/10 dark:bg-mint/20 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-mint">
                    <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
                    <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
                </svg>
            </div>
        </div>

        <!-- Quote Text -->
        <blockquote class="mb-6">
            <p class="text-xl md:text-2xl font-medium text-zinc-800 dark:text-zinc-200 leading-relaxed italic">
                "{{ $quote['text'] }}"
            </p>
        </blockquote>

        <!-- Author -->
        <cite class="text-sm font-bold text-zinc-500 dark:text-zinc-400 not-italic">
            — {{ $quote['author'] }}
        </cite>

        <!-- Refresh Button -->
        <div class="mt-8">
            <button
                wire:click="refreshQuote"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest text-zinc-500 hover:text-zinc-950 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-all group"
                title="Get new quote"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:rotate-180 transition-transform duration-300">
                    <path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                    <path d="M3 3v5h5"/>
                    <path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/>
                    <path d="M16 21h5v-5"/>
                </svg>
                New Quote
            </button>
        </div>
    </div>

    <!-- Decorative Background Elements -->
    <div class="absolute top-1/2 left-10 -translate-y-1/2 opacity-5 pointer-events-none">
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="currentColor" class="text-mint">
            <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
        </svg>
    </div>
    <div class="absolute top-1/2 right-10 -translate-y-1/2 opacity-5 pointer-events-none rotate-180">
        <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24" fill="currentColor" class="text-violet">
            <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"/>
        </svg>
    </div>
</section>
