<div class="fixed bottom-6 right-6 z-[9999]"
     x-data="{
        isOpen: @entangle('isOpen'),
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
            // Check if this is a game button click
            if (message.startsWith('game:')) {
                const gameType = message.replace('game:', '');
                await this.startGame(gameType);
                return;
            }

            // Check if this is ending game - treat as normal message
            if (message === 'game:end') {
                // Send as normal message to be processed
                await this.sendMessage('stop game');
                return;
            }

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

        async startGame(gameType) {
            this.isThinking = true;
            try {
                await this.$wire.startGame(gameType);
                this.isThinking = false;
            } catch (e) {
                this.isThinking = false;
                console.error('Error starting game:', e);
            }
        },

        async submitGameAnswer(answer) {
            this.isThinking = true;
            try {
                await this.$wire.submitGameAnswer(answer);
                this.isThinking = false;
            } catch (e) {
                this.isThinking = false;
                console.error('Error submitting answer:', e);
            }
        },

        scrollToBottom() {
            const container = document.getElementById('chat-messages');
            if (container) {
                container.scrollTo({
                    top: container.scrollHeight,
                    behavior: 'smooth'
                });
            }
        },

        attemptClose(hasActiveGame) {
            if (hasActiveGame) {
                if (confirm('Game sedang berlangsung! Yakin ingin menutup chat? Game akan diakhiri.')) {
                    this.$wire.endGame().then(() => {
                        this.isOpen = false;
                    });
                }
            } else {
                this.isOpen = false;
            }
        }
    }"
     x-init="$wire.on('chat-updated', () => { $nextTick(() => scrollToBottom()); });"
>
    {{-- Touch-friendly styles --}}
    <style>
        .touch-manipulation {
            touch-action: manipulation;
            -webkit-tap-highlight-color: transparent;
        }
        input[type="number"].touch-manipulation {
            font-size: 16px;
        }
        .touch-manipulation {
            transition: transform 0.1s ease, background-color 0.2s ease, opacity 0.2s ease;
        }
        .touch-manipulation:active:not(:disabled) {
            transform: scale(0.96);
        }
    </style>
    {{-- Chat Toggle Button --}}
    <button
        x-show="!isOpen"
        x-transition:enter="transition ease-out duration-300 delay-200"
        x-transition:enter-start="opacity-0 scale-50"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-50"
        @click="isOpen = true"
        type="button"
        class="absolute bottom-0 right-0 flex items-center justify-center w-12 h-12 bg-mint rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-110"
        aria-label="Open chat assistant">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-zinc-950" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
        </svg>
    </button>

    {{-- Chat Window --}}
    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        @click.outside="attemptClose($wire.activeGame !== null)"
        class="absolute bottom-0 right-0 flex flex-col w-80 sm:w-96 h-[480px] bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden origin-bottom-right"
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
                {{-- Context Menu Dropdown --}}
                <div x-data="{ showContext: false }" class="relative">
                    <button @click="showContext = !showContext" type="button"
                            class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors {{ !empty($userContexts) ? 'text-mint' : '' }}"
                            title="Your Info">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </button>

                    {{-- Context Dropdown --}}
                    <div x-show="showContext" @click.away="showContext = false"
                         x-transition class="absolute right-0 top-full mt-1 w-56 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-zinc-200 dark:border-zinc-700 z-50"
                         style="display: none;">
                        <div class="p-3">
                            <p class="text-xs font-semibold text-zinc-500 uppercase mb-2">Info Tersimpan</p>
                            @if(!empty($userContexts))
                                <div class="space-y-1.5">
                                    @foreach($userContexts as $type => $value)
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="capitalize text-zinc-600 dark:text-zinc-400">{{ str_replace('_', ' ', $type) }}:</span>
                                            <span class="font-medium text-zinc-900 dark:text-zinc-100 truncate max-w-[100px]" title="{{ $value }}">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-700">
                                    <button wire:click="clearContext" @click="showContext = false"
                                            class="text-xs text-red-500 hover:text-red-600 w-full text-left">
                                        Hapus Semua Info
                                    </button>
                                </div>
                            @else
                                <p class="text-xs text-zinc-400 italic">Belum ada info tersimpan</p>
                            @endif
                        </div>
                    </div>
                </div>

                <button wire:click="clearChat" type="button" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors" title="Clear chat">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
                <button @click="attemptClose($wire.activeGame !== null)" type="button" class="p-2 text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
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
                    <div class="flex justify-end animate-in fade-in slide-in-from-bottom-3 duration-300" wire:key="user-msg-{{ $index }}">
                        <div class="max-w-[80%] flex flex-col items-end">
                            <div class="bg-mint text-zinc-950 rounded-2xl rounded-tr-sm px-4 py-2 shadow-sm">
                                <p class="text-sm">{{ $msg['content'] }}</p>
                            </div>
                            @if(isset($msg['timestamp']))
                            <span class="text-[10px] text-zinc-400 mt-1 mr-1">{{ \Carbon\Carbon::parse($msg['timestamp'])->format('H:i') }}</span>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Assistant Message --}}
                    @php
                        $formatted = $this->formatMessage($msg['content']);
                    @endphp
                    <div class="flex justify-start animate-in fade-in slide-in-from-bottom-3 duration-300 delay-100" wire:key="assistant-msg-{{ $index }}">
                        <div class="max-w-[85%] flex flex-col items-start"
                             x-data="{
                                 html: '',
                                 decodedHtml: @js($formatted['text'] ?? ''),
                                 index: 0,
                                 isTyping: false,
                                 init() {
                                     if (!this.decodedHtml) return;
                                     @if($index === count($chatHistory) - 1)
                                         this.isTyping = true;
                                         this.type();
                                     @else
                                         this.html = this.decodedHtml;
                                     @endif
                                 },
                                 type() {
                                     if(this.index < this.decodedHtml.length) {
                                         let char = this.decodedHtml.charAt(this.index);
                                         if (char === '<') {
                                             let end = this.decodedHtml.indexOf('>', this.index);
                                             if (end !== -1) {
                                                 this.html += this.decodedHtml.substring(this.index, end + 1);
                                                 this.index = end + 1;
                                             } else {
                                                 this.html += char; this.index++;
                                             }
                                         } else if (char === '&') {
                                             let end = this.decodedHtml.indexOf(';', this.index);
                                             if (end !== -1 && end - this.index < 10) {
                                                 this.html += this.decodedHtml.substring(this.index, end + 1);
                                                 this.index = end + 1;
                                             } else {
                                                 this.html += char; this.index++;
                                             }
                                         } else {
                                             this.html += char; this.index++;
                                         }
                                         
                                         if (this.index % 2 === 0) {
                                             const container = document.getElementById('chat-messages');
                                             if (container) container.scrollTop = container.scrollHeight;
                                         }
                                         
                                         setTimeout(() => { this.type(); }, Math.random() * 15 + 10);
                                     } else {
                                         this.isTyping = false;
                                         const container = document.getElementById('chat-messages');
                                         if (container) container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
                                     }
                                 }
                             }"
                        >
                            <div class="bg-zinc-100 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 rounded-2xl rounded-tl-sm px-4 py-2 shadow-sm">
                            @if (!empty($formatted['text']))
                                <div class="text-sm leading-relaxed" x-html="html"></div>
                            @endif
                            @if (!empty($formatted['buttons']))
                                <div x-show="!isTyping" x-transition.opacity.duration.500ms class="flex flex-wrap gap-2 {{ !empty($formatted['text']) ? 'mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-700' : '' }}" style="display: none;">
                                    @foreach ($formatted['buttons'] as $button)
                                        @if ($button['isGameAction'])
                                            @if(str_contains($button['label'], 'Stop'))
                                                <button
                                                    type="button"
                                                    wire:click="endGame"
                                                    class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2.5 text-sm sm:text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800 rounded-xl hover:opacity-90 active:scale-95 transition-all touch-manipulation"
                                                >
                                                    <span>⏹️</span>
                                                    {{ $button['label'] }}
                                                </button>
                                            @else
                                                <button
                                                    type="button"
                                                    @click="sendMessage('{{ $button['url'] }}')"
                                                    class="min-h-[44px] inline-flex items-center gap-2 px-4 py-2.5 text-sm sm:text-xs font-semibold bg-mint text-zinc-950 rounded-xl hover:opacity-90 active:scale-95 hover:-translate-y-0.5 hover:shadow-md transition-all touch-manipulation"
                                                >
                                                    @if(str_contains($button['label'], 'Math'))
                                                        <span>🧮</span>
                                                    @elseif(str_contains($button['label'], 'Teka'))
                                                        <span>🧩</span>
                                                    @elseif(str_contains($button['label'], 'Quiz'))
                                                        <span>📚</span>
                                                    @endif
                                                    {{ $button['label'] }}
                                                </button>
                                            @endif
                                        @else
                                            <a href="{{ $button['url'] }}" class="min-h-[44px] inline-flex items-center px-4 py-2.5 text-sm sm:text-xs font-semibold bg-mint text-zinc-950 rounded-xl hover:opacity-90 active:scale-95 hover:-translate-y-0.5 hover:shadow-md transition-all touch-manipulation">
                                                {{ $button['label'] }}
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            @if (!empty($formatted['suggestions']))
                                <div x-show="!isTyping" x-transition.opacity.duration.500ms class="mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-700" style="display: none;">
                                    <p class="text-[10px] uppercase tracking-wider text-zinc-400 mb-2">Pertanyaan lanjutan</p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($formatted['suggestions'] as $suggestion)
                                            <button
                                                type="button"
                                                @click="sendMessage('{{ addslashes($suggestion['question']) }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs border border-zinc-300 dark:border-zinc-600 text-zinc-600 dark:text-zinc-400 rounded-lg hover:border-mint hover:text-mint dark:hover:border-mint dark:hover:text-mint hover:-translate-y-0.5 shadow-sm hover:shadow transition-all cursor-pointer"
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

                            {{-- Game Inputs --}}
                            @if (!empty($formatted['gameInputs']))
                                <div x-show="!isTyping" x-transition.opacity.duration.500ms class="mt-3 pt-2 border-t border-zinc-200 dark:border-zinc-700" style="display: none;">
                                    @foreach ($formatted['gameInputs'] as $input)
                                        @if ($input['type'] === 'number')
                                            <div x-data="{ answer: '', isSubmitting: false }" class="flex flex-col sm:flex-row gap-2" wire:loading.class="opacity-70">
                                                <input
                                                    type="number"
                                                    inputmode="numeric"
                                                    pattern="[0-9]*"
                                                    x-model="answer"
                                                    @keydown.enter.prevent="if(answer && !isSubmitting) { isSubmitting = true; submitGameAnswer(parseInt(answer)).then(() => { answer = ''; isSubmitting = false; }) }"
                                                    placeholder="Ketik jawaban..."
                                                    class="flex-1 min-h-[44px] px-4 py-3 text-base sm:text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-mint focus:border-transparent text-zinc-900 dark:text-white touch-manipulation"
                                                    style="font-size: 16px;"
                                                >
                                                <button
                                                    type="button"
                                                    @click="if(answer && !isSubmitting) { isSubmitting = true; submitGameAnswer(parseInt(answer)).then(() => { answer = ''; isSubmitting = false; }) }"
                                                    :disabled="!answer || isSubmitting"
                                                    class="min-h-[44px] px-6 py-3 text-base sm:text-sm font-semibold bg-mint text-zinc-950 rounded-xl hover:opacity-90 active:scale-95 hover:-translate-y-0.5 hover:shadow-md transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 touch-manipulation"
                                                >
                                                    <span x-show="!isSubmitting">✓ Submit</span>
                                                    <span x-show="isSubmitting" class="flex items-center justify-center gap-1">
                                                        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </span>
                                                </button>
                                            </div>
                                        @elseif ($input['type'] === 'select')
                                            {{-- Option Buttons (lebih mobile-friendly dari select) --}}
                                            <div class="grid grid-cols-1 gap-2">
                                                @foreach ($input['options'] as $index => $option)
                                                    <button
                                                        type="button"
                                                        wire:key="option-{{ $index }}"
                                                        @click="submitGameAnswer({{ $index }})"
                                                        class="min-h-[48px] w-full px-4 py-3 text-left text-base sm:text-sm font-medium bg-white dark:bg-zinc-800 border-2 border-zinc-200 dark:border-zinc-700 rounded-xl hover:border-mint hover:bg-mint/5 dark:hover:border-mint dark:hover:bg-mint/10 active:scale-[0.98] active:bg-mint/20 hover:-translate-y-0.5 hover:shadow-sm transition-all touch-manipulation flex items-center gap-3"
                                                    >
                                                        <span class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-zinc-100 dark:bg-zinc-700 rounded-lg text-sm font-bold text-zinc-600 dark:text-zinc-400">
                                                            {{ chr(65 + $index) }}
                                                        </span>
                                                        <span class="flex-1 text-zinc-900 dark:text-white">{{ $option }}</span>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                            </div>
                            @if(isset($msg['timestamp']))
                            <span class="text-[10px] text-zinc-400 mt-1 ml-1">{{ \Carbon\Carbon::parse($msg['timestamp'])->format('H:i') }}</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach

            {{-- Temporary User Messages (Instant) --}}
            <template x-for="(msg, index) in tempMessages" :key="index">
                <div class="flex justify-end animate-in fade-in slide-in-from-bottom-2 duration-200">
                    <div class="max-w-[80%] flex flex-col items-end">
                        <div class="bg-mint text-zinc-950 rounded-2xl rounded-tr-sm px-4 py-2 shadow-sm">
                            <p class="text-sm" x-text="msg.content"></p>
                        </div>
                        <span class="text-[10px] text-zinc-400 mt-1 mr-1">Terkirim</span>
                    </div>
                </div>
            </template>

            {{-- AI Thinking Animation --}}
            <div x-show="isThinking" x-transition class="flex justify-start animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div class="max-w-[85%] bg-zinc-100 dark:bg-zinc-800 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
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
            <form @submit.prevent="send" class="flex gap-2 items-end">
                <textarea
                    x-model="inputMessage"
                    x-ref="messageInput"
                    @input="
                        $el.style.height = '44px';
                        $el.style.height = Math.min($el.scrollHeight, 120) + 'px';
                    "
                    @keydown.enter.prevent="if(!event.shiftKey) send()"
                    placeholder="{{ $activeGame ? 'Ketik jawaban atau stop...' : 'Tulis pesan...' }}"
                    :disabled="isThinking"
                    class="flex-1 min-h-[44px] max-h-[120px] px-4 py-3 text-base sm:text-sm bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-mint focus:border-transparent text-zinc-900 dark:text-white placeholder-zinc-400 disabled:opacity-50 touch-manipulation resize-none overflow-y-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]"
                    style="font-size: 16px; height: 44px; line-height: 1.4;"
                    maxlength="500"
                    inputmode="{{ $activeGame && $activeGame['type'] === 'math' ? 'numeric' : 'text' }}"
                    rows="1"
                ></textarea>
                <button
                    type="submit"
                    :disabled="isThinking || !inputMessage.trim()"
                    class="flex items-center justify-center min-w-[44px] min-h-[44px] w-11 h-11 bg-mint rounded-xl hover:opacity-90 active:scale-95 transition-all disabled:opacity-50 touch-manipulation"
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
