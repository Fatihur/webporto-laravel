<div class="p-6 max-w-4xl mx-auto">
    @php use Illuminate\Support\Str; @endphp
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.newsletter.index') }}" wire:navigate class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m15 18-6-6 6-6"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold">Send Newsletter</h1>
            <p class="text-zinc-500 mt-1">Compose and send newsletter to {{ number_format($activeSubscribersCount) }} active subscribers.</p>
        </div>
    </div>

    {{-- Form --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 space-y-6">
            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium mb-2">Subject</label>
                <input
                    type="text"
                    wire:model="subject"
                    placeholder="Enter newsletter subject..."
                    class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-0 rounded-xl text-sm focus:ring-2 focus:ring-mint"
                >
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-sm font-medium mb-2">Content</label>
                <textarea
                    wire:model="content"
                    rows="12"
                    placeholder="Write your newsletter content here... You can use Markdown formatting."
                    class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-0 rounded-xl text-sm focus:ring-2 focus:ring-mint resize-none"
                ></textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-zinc-500">
                    Tip: You can use Markdown formatting. Links will be automatically converted.
                </p>
            </div>

            {{-- Test Mode --}}
            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-xl p-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="testMode" class="rounded border-zinc-300 text-mint focus:ring-mint">
                    <span class="text-sm font-medium">Send test email first</span>
                </label>

                @if($testMode)
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">Test Email Address</label>
                        <input
                            type="email"
                            wire:model="testEmail"
                            placeholder="your@email.com"
                            class="w-full px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm focus:ring-2 focus:ring-mint"
                        >
                        @error('testEmail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif
            </div>

            {{-- Preview --}}
            @if($previewMode)
                <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
                    <div class="bg-zinc-100 dark:bg-zinc-900 px-4 py-2 text-sm font-medium border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                        <span>Preview</span>
                        <button wire:click="togglePreview" class="text-zinc-500 hover:text-zinc-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 bg-white">
                        <h2 class="text-xl font-bold mb-4">{{ $subject }}</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! Str::markdown($content) !!}
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <button
                wire:click="togglePreview"
                class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white font-medium text-sm"
            >
                {{ $previewMode ? 'Hide Preview' : 'Show Preview' }}
            </button>

            <div class="flex items-center gap-3">
                @if($testMode)
                    <button
                        wire:click="sendTest"
                        wire:loading.attr="disabled"
                        class="px-6 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors font-medium text-sm disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="sendTest">Send Test</span>
                        <span wire:loading wire:target="sendTest">Sending...</span>
                    </button>
                @endif

                <button
                    wire:click="sendNewsletter"
                    wire:loading.attr="disabled"
                    wire:confirm="Are you sure you want to send this newsletter to {{ number_format($activeSubscribersCount) }} subscribers?"
                    class="px-6 py-2 bg-mint text-zinc-950 rounded-xl hover:scale-105 transition-transform font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="sendNewsletter">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                            <path d="m22 2-7 20-4-9-9-4Z"/>
                            <path d="M22 2 11 13"/>
                        </svg>
                        Send Newsletter
                    </span>
                    <span wire:loading wire:target="sendNewsletter">Sending...</span>
                </button>
            </div>
        </div>
    </div>
</div>
