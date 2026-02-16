<div class="p-6 max-w-4xl mx-auto" x-data="{ init() { $wire.testEmail = '{{ auth()?->user()?->email ?? '' }}' } }">
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

    {{-- Progress Bar (visible during sending) --}}
    @if($isSending)
    <div class="bg-mint/10 border border-mint/30 rounded-xl p-6 mb-6">
        <div class="flex items-center justify-between mb-3">
            <span class="font-medium text-zinc-900 dark:text-white">Sending newsletter...</span>
            <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $sendProgress }} / {{ $totalSubscribers }}</span>
        </div>
        <div class="w-full bg-zinc-200 dark:bg-zinc-700 rounded-full h-2.5">
            <div class="bg-mint h-2.5 rounded-full transition-all duration-300" style="width: {{ $totalSubscribers > 0 ? ($sendProgress / $totalSubscribers) * 100 : 0 }}%"></div>
        </div>
    </div>
    @endif

    {{-- Form --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
        <div class="p-6 space-y-6">
            {{-- Subject --}}
            <div>
                <label class="block text-sm font-medium mb-2">Subject <span class="text-red-500">*</span></label>
                <input
                    type="text"
                    wire:model="subject"
                    placeholder="Enter newsletter subject..."
                    class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-0 rounded-xl text-sm focus:ring-2 focus:ring-mint"
                    :disabled="$wire.isSending"
                >
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content --}}
            <div>
                <label class="block text-sm font-medium mb-2">Content <span class="text-red-500">*</span></label>
                <textarea
                    wire:model="content"
                    rows="12"
                    placeholder="Write your newsletter content here..."
                    class="w-full px-4 py-3 bg-zinc-100 dark:bg-zinc-900 border-0 rounded-xl text-sm focus:ring-2 focus:ring-mint resize-none"
                    :disabled="$wire.isSending"
                ></textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-zinc-500">
                    Supports plain text and basic formatting. Links will be automatically converted.
                </p>
            </div>

            {{-- Test Mode --}}
            <div class="bg-zinc-50 dark:bg-zinc-900/50 rounded-xl p-4">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" wire:model="testMode" class="rounded border-zinc-300 text-mint focus:ring-mint" :disabled="$wire.isSending">
                    <span class="text-sm font-medium">Send test email first</span>
                </label>

                @if($testMode)
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-2">Test Email Address <span class="text-red-500">*</span></label>
                        <input
                            type="email"
                            wire:model="testEmail"
                            placeholder="your@email.com"
                            class="w-full px-4 py-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm focus:ring-2 focus:ring-mint"
                            :disabled="$wire.isSending"
                        >
                        @error('testEmail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-zinc-500">We'll send a test email to this address before sending to all subscribers.</p>
                    </div>
                @endif
            </div>

            {{-- Preview --}}
            @if($previewMode)
                <div class="border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-hidden">
                    <div class="bg-zinc-100 dark:bg-zinc-900 px-4 py-2 text-sm font-medium border-b border-zinc-200 dark:border-zinc-700 flex justify-between items-center">
                        <span>Email Preview</span>
                        <button wire:click="togglePreview" class="text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 bg-white">
                        <div class="border-b border-zinc-200 pb-4 mb-4">
                            <span class="text-sm text-zinc-500">Subject: </span>
                            <span class="font-medium">{{ $subject }}</span>
                        </div>
                        <div class="prose dark:prose-invert max-w-none text-zinc-800">
                            {!! nl2br(e($content)) !!}
                        </div>
                        <div class="mt-8 pt-4 border-t border-zinc-200 text-sm text-zinc-500">
                            <p>Thanks,<br>{{ config('app.name') }}</p>
                            <p class="mt-4">
                                <a href="#" class="text-zinc-400 underline">Unsubscribe</a>
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 flex flex-col sm:flex-row justify-between items-center gap-4">
            <button
                wire:click="togglePreview"
                wire:loading.attr="disabled"
                wire:target="togglePreview"
                class="px-4 py-2 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white font-medium text-sm"
                :disabled="$wire.isSending"
            >
                {{ $previewMode ? 'Hide Preview' : 'Show Preview' }}
            </button>

            <div class="flex items-center gap-3">
                @if($testMode)
                    <button
                        wire:click="sendTest"
                        wire:loading.attr="disabled"
                        wire:target="sendTest"
                        class="px-6 py-2 bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 rounded-xl hover:bg-zinc-200 dark:hover:bg-zinc-600 transition-colors font-medium text-sm disabled:opacity-50"
                        :disabled="$wire.isSending"
                    >
                        <span wire:loading.remove wire:target="sendTest">Send Test</span>
                        <span wire:loading wire:target="sendTest">Sending...</span>
                    </button>
                @endif

                <button
                    wire:click="sendNewsletter"
                    wire:loading.attr="disabled"
                    wire:target="sendNewsletter"
                    wire:confirm="Are you sure you want to send this newsletter to {{ number_format($activeSubscribersCount) }} subscribers?"
                    class="px-6 py-2 bg-mint text-zinc-950 rounded-xl hover:scale-105 transition-transform font-bold text-sm disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100"
                    :disabled="$wire.isSending || $activeSubscribersCount === 0"
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

    {{-- Info Card --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 dark:text-blue-400 mt-0.5">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4"/>
                <path d="M12 8h.01"/>
            </svg>
            <div class="text-sm text-blue-800 dark:text-blue-200">
                <p class="font-medium">Before sending:</p>
                <ul class="mt-1 list-disc list-inside space-y-1">
                    <li>Make sure queue worker is running: <code class="bg-blue-100 dark:bg-blue-800 px-1.5 py-0.5 rounded">php artisan queue:work</code></li>
                    <li>Send a test email first to verify formatting</li>
                    <li>Emails are sent via queue to prevent timeout</li>
                </ul>
            </div>
        </div>
    </div>
</div>
