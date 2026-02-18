<div class="fixed bottom-6 right-6 z-[9999]"
     x-data="{
        open: @entangle('isOpen'),
        inputMessage: '',
        tempMessages: [],
        isThinking: false,

        async send() {
            if (!this.inputMessage.trim()) return;

            const message = this.inputMessage.trim();
            this.inputMessage = '';

            await this.sendMessage(message);
        },

        async sendMessage(message) {
            // Tambahkan pesan user ke temporary messages
            this.tempMessages.push({ role: 'user', content: message });
            this.isThinking = true;

            this.$nextTick(() => {
                this.scrollToBottom();
            });

            // Kirim ke server
            try {
                await this.$wire.processMessage(message);
                this.isThinking = false;
                this.tempMessages = []; // Clear temp messages karena sudah di server
            } catch (e) {
                this.isThinking = false;
                this.tempMessages = [];
                console.error('Error:', e);
            }
        },

        scrollToBottom() {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }"
     x-init="$wire.on('chat-updated', () => {
         $nextTick(() => scrollToBottom());
     });"
>
    {{-- Chat Toggle Button --}}
    <button
        x-show="!open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-75"
        wire:click="toggle"
        type="button"
        class="flex items-center justify-center w-12 h-12 bg-mint rounded-full shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110"
        aria-label="Open chat assistant">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-zinc-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>

    {{-- Chat Window --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        @click.away="$wire.close()"
        class="flex flex-col w-80 sm:w-96 h-[480px] bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden origin-bottom-right"
        style="display: none;"
    >
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 bg-zinc-100 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-mint flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-sm text-zinc-900 dark:text-white">Fay</h3>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Asisten AI</p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button wire:click="clearChat" type="button" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors" title="Clear chat">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button wire:click="close" type="button" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Large Hi Animation (Above Messages) --}}
        @if(empty($chatHistory) || count($chatHistory) <= 1)
            <div class="flex justify-center pt-6 pb-2 bg-gradient-to-b from-mint/5 to-transparent">
                <div class="w-40 h-40">
                    <lottie-player
                        src="{{ asset('hi.json') }}"
                        background="transparent"
                        speed="1"
                        loop
                        autoplay>
                    </lottie-player>
                </div>
            </div>
        @endif

        {{-- Messages Area --}}
        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar" id="chat-messages">
            @foreach ($chatHistory as $index => $msg)
                @if ($msg['role'] === 'user')
                    {{-- User Message --}}
                    <div class="flex justify-end" wire:key="user-msg-{{ $index }}">
                        <div class="max-w-[80%] bg-mint text-zinc-950 rounded-2xl rounded-tr-sm px-4 py-2">
                            <p class="text-sm">{{ $msg['content'] }}</p>
                        </div>
                    </div>
                @else
                    {{-- Assistant Message --}}
                    @php
                        $formatted = $this->formatMessage($msg['content']);
                    @endphp
                    <div class="flex justify-start" wire:key="assistant-msg-{{ $index }}">
                        <div class="max-w-[85%] bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-2xl rounded-tl-sm px-4 py-2">
                            @if (!empty($formatted['text']))
                                <div class="text-sm leading-relaxed">{!! $formatted['text'] !!}</div>
                            @endif
                            @if (!empty($formatted['buttons']))
                                <div class="flex flex-wrap gap-2 {{ !empty($formatted['text']) ? 'mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-700' : '' }}">
                                    @foreach ($formatted['buttons'] as $button)
                                        <a href="{{ $button['url'] }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium bg-mint text-zinc-950 rounded-lg hover:opacity-80 transition-opacity">
                                            {{ $button['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                            @if (!empty($formatted['suggestions']))
                                <div class="mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                    <p class="text-[10px] uppercase tracking-wider text-zinc-400 mb-2">Pertanyaan lanjutan</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($formatted['suggestions'] as $suggestion)
                                            <button
                                                type="button"
                                                @click="sendMessage('{{ addslashes($suggestion['question']) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 rounded-lg hover:border-mint hover:text-mint dark:hover:border-mint dark:hover:text-mint transition-colors cursor-pointer"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                </svg>
                                                {{ $suggestion['label'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Temporary User Messages (Instant) --}}
            <template x-for="(msg, index) in tempMessages" :key="index">
                <div class="flex justify-end">
                    <div class="max-w-[80%] bg-mint text-zinc-950 rounded-2xl rounded-tr-sm px-4 py-2">
                        <p class="text-sm" x-text="msg.content"></p>
                    </div>
                </div>
            </template>

            {{-- AI Thinking Animation --}}
            <div x-show="isThinking" x-transition class="flex justify-start">
                <div class="max-w-[85%] bg-zinc-100 dark:bg-zinc-800 rounded-2xl rounded-tl-sm px-4 py-3">
                    <div class="flex items-center gap-3">
                        <div class="w-16 h-16">
                            <lottie-player
                                src="{{ asset('thinking.json') }}"
                                background="transparent"
                                speed="1"
                                loop
                                autoplay>
                            </lottie-player>
                        </div>
                        <span class="text-xs text-zinc-500 dark:text-zinc-400">Fay sedang berpikir...</span>
                    </div>
                </div>
            </div>

            {{-- Server Loading State (Backup) --}}
            @if ($isLoading)
                <div class="flex justify-start" wire:key="thinking-server">
                    <div class="max-w-[85%] bg-zinc-100 dark:bg-zinc-800 rounded-2xl rounded-tl-sm px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-16 h-16">
                                <lottie-player
                                    src="{{ asset('thinking.json') }}"
                                    background="transparent"
                                    speed="1"
                                    loop
                                    autoplay>
                                </lottie-player>
                            </div>
                            <span class="text-xs text-zinc-500 dark:text-zinc-400">Fay sedang berpikir...</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Input Area --}}
        <div class="p-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
            <form @submit.prevent="send" class="flex gap-2">
                <input
                    type="text"
                    x-model="inputMessage"
                    placeholder="Tulis pesan..."
                    :disabled="isThinking"
                    class="flex-1 px-4 py-2 text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-mint focus:border-transparent text-zinc-900 dark:text-white placeholder-zinc-400 disabled:opacity-50"
                    maxlength="500"
                >
                <button
                    type="submit"
                    :disabled="isThinking || !inputMessage.trim()"
                    class="flex items-center justify-center w-10 h-10 bg-mint rounded-xl hover:opacity-80 transition-opacity disabled:opacity-50"
                >
                    <svg x-show="!isThinking" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <svg x-show="isThinking" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-zinc-950 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    {{-- Lottie Player Script --}}
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
</div>
