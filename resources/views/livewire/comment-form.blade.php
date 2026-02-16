<div
    class="bg-zinc-50 dark:bg-zinc-900 rounded-3xl p-6 md:p-8 mb-8"
    x-data="{ parentId: @entangle('parentId'), replyingToName: @entangle('replyingToName') }"
    @set-reply-to.window="parentId = $event.detail.parentId; replyingToName = $event.detail.name"
    x-init="$wire.on('commentAdded', () => { parentId = null; replyingToName = null; })"
>
    <h4 class="text-lg font-bold mb-6">
        @if($replyingToName)
            Replying to {{ $replyingToName }}
            <button wire:click="cancelReply" class="ml-2 text-xs font-medium text-zinc-500 hover:text-red-500">Cancel</button>
        @else
            Leave a Comment
        @endif
    </h4>

    @if (session('message'))
        <div class="mb-6 p-4 bg-mint/10 text-zinc-950 dark:text-mint rounded-2xl text-sm font-medium">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                {{ session('message') }}
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label for="name" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2">
                    Name
                </label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-mint focus:border-transparent dark:text-white transition-all"
                    placeholder="Your name"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-mint focus:border-transparent dark:text-white transition-all"
                    placeholder="your@email.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="content" class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2">
                Comment
            </label>
            <textarea
                id="content"
                wire:model="content"
                rows="4"
                class="w-full px-4 py-3 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:ring-2 focus:ring-mint focus:border-transparent dark:text-white transition-all resize-none"
                placeholder="Share your thoughts..."
            ></textarea>
            @error('content')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <input type="hidden" wire:model="parentId">

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-bold px-6 py-3 rounded-full hover:scale-105 transition-transform disabled:opacity-50 disabled:cursor-not-allowed"
        >
            <span wire:loading.remove>Submit Comment</span>
            <span wire:loading>Submitting...</span>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="m12 5 7 7-7 7"/>
            </svg>
        </button>
    </form>
</div>
