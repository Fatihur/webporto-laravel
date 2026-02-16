<div class="fixed bottom-6 right-6 z-50" x-data="chatBot()" x-init="init()">
    {{-- Floating Action Button --}}
    <button
        wire:click="toggle"
        x-show="!$wire.isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-75"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-75"
        class="group flex items-center justify-center w-14 h-14 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-full shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105"
        aria-label="Open chat"
    >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 bg-mint text-zinc-950 text-xs font-bold rounded-full flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    {{-- Chat Widget --}}
    <div
        x-show="$wire.isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="absolute bottom-0 right-0 w-80 sm:w-96 bg-white dark:bg-zinc-950 rounded-3xl shadow-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden"
        style="display: none;"
        x-cloak
    >
        {{-- Header --}}
        <div class="bg-zinc-950 dark:bg-zinc-900 px-5 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative w-9 h-9 bg-mint/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-mint" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-zinc-950 rounded-full"></span>
                </div>
                <div>
                    <h3 class="text-white font-bold text-sm">AI Assistant</h3>
                    <p class="text-zinc-400 text-xs flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        Online
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-1">
                <button
                    wire:click="clearChat"
                    x-show="$wire.messages.length > 1"
                    x-transition
                    class="p-2 text-zinc-400 hover:text-white transition-colors"
                    title="Clear chat"
                    aria-label="Clear chat"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
                <button
                    wire:click="toggle"
                    class="p-2 text-zinc-400 hover:text-white transition-colors"
                    aria-label="Close chat"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Messages Area --}}
        <div
            class="h-80 overflow-y-auto p-4 space-y-3 bg-zinc-50 dark:bg-zinc-900/50 chat-scrollbar"
            x-ref="messageContainer"
            @scroll="handleScroll"
        >
            <template x-for="(msg, index) in messages" :key="msg.id">
                <div>
                    {{-- User Message --}}
                    <template x-if="msg.role === 'user'">
                        <div class="flex justify-end">
                            <div class="max-w-[85%] bg-zinc-950 dark:bg-zinc-800 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 shadow-sm">
                                <p class="text-sm whitespace-pre-wrap" x-text="msg.content"></p>
                            </div>
                        </div>
                    </template>

                    {{-- Assistant Message --}}
                    <template x-if="msg.role === 'assistant'">
                        <div class="flex justify-start group">
                            <div class="max-w-[90%] bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm border border-zinc-200 dark:border-zinc-700 relative">
                                {{-- Typing indicator for active typing --}}
                                <template x-if="msg.isTyping && typingMessageId === msg.id">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="flex space-x-1">
                                            <span class="w-1.5 h-1.5 bg-mint rounded-full animate-bounce"></span>
                                            <span class="w-1.5 h-1.5 bg-mint rounded-full animate-bounce" style="animation-delay: 0.1s"></span>
                                            <span class="w-1.5 h-1.5 bg-mint rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                                        </div>
                                        <span class="text-xs text-zinc-400">AI is typing...</span>
                                    </div>
                                </template>

                                {{-- Message content with typing effect --}}
                                <div class="text-sm prose dark:prose-invert prose-sm max-w-none">
                                    <template x-if="msg.isTyping && typingMessageId === msg.id">
                                        <div>
                                            <span x-html="formatMarkdown(typedContent)"></span>
                                            <span class="inline-block w-2 h-4 bg-mint ml-0.5 animate-pulse"></span>
                                        </div>
                                    </template>
                                    <template x-if="!(msg.isTyping && typingMessageId === msg.id)">
                                        <div x-html="formatMarkdown(msg.content || msg.fullContent || '')"></div>
                                    </template>
                                </div>

                                {{-- Skip typing button --}}
                                <template x-if="msg.isTyping && typingMessageId === msg.id">
                                    <button
                                        @click="skipTyping()"
                                        class="absolute -bottom-8 right-0 text-xs bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-600 dark:text-zinc-300 px-2 py-1 rounded transition-colors"
                                    >
                                        Skip
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            {{-- Thinking State (Alpine-driven for instant feedback) --}}
            <div x-show="isWaiting" x-transition class="flex justify-start">
                <div class="bg-white dark:bg-zinc-800 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm border border-zinc-200 dark:border-zinc-700">
                    <div class="flex items-center gap-3">
                        <div class="flex space-x-1">
                            <span class="w-2 h-2 bg-mint rounded-full animate-bounce"></span>
                            <span class="w-2 h-2 bg-mint rounded-full animate-bounce" style="animation-delay: 0.15s"></span>
                            <span class="w-2 h-2 bg-mint rounded-full animate-bounce" style="animation-delay: 0.3s"></span>
                        </div>
                        <span class="text-xs text-zinc-400">AI is thinking...</span>
                    </div>
                </div>
            </div>

            {{-- Error Message --}}
            @if($error)
                <div class="flex justify-center">
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 rounded-xl px-4 py-2 text-xs flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $error }}
                    </div>
                </div>
            @endif
        </div>

        {{-- Quick Suggestions --}}
        <div
            x-show="showSuggestions && messages.length <= 2"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="px-4 py-2 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-100 dark:border-zinc-800"
        >
            <p class="text-[10px] text-zinc-400 mb-2 uppercase tracking-wider font-medium">Suggestions</p>
            <div class="flex flex-wrap gap-2">
                <button
                    @click="sendSuggestion('Tell me about your projects')"
                    class="text-xs bg-white dark:bg-zinc-800 hover:bg-mint/10 dark:hover:bg-mint/20 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full px-3 py-1.5 transition-colors"
                >
                    Your projects
                </button>
                <button
                    @click="sendSuggestion('What skills do you have?')"
                    class="text-xs bg-white dark:bg-zinc-800 hover:bg-mint/10 dark:hover:bg-mint/20 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full px-3 py-1.5 transition-colors"
                >
                    Your skills
                </button>
                <button
                    @click="sendSuggestion('Show me your blog posts')"
                    class="text-xs bg-white dark:bg-zinc-800 hover:bg-mint/10 dark:hover:bg-mint/20 text-zinc-600 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-full px-3 py-1.5 transition-colors"
                >
                    Blog posts
                </button>
            </div>
        </div>

        {{-- Input Area --}}
        <div class="p-4 bg-white dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800">
            <form
                @submit.prevent="submitMessage()"
                class="flex items-center gap-2"
            >
                <textarea
                    x-model="inputMessage"
                    x-ref="textarea"
                    placeholder="Type a message..."
                    rows="1"
                    @keydown.enter.prevent="if (!$event.shiftKey) submitMessage()"
                    class="flex-1 resize-none bg-zinc-100 dark:bg-zinc-900 border-0 rounded-xl text-sm py-2.5 px-4 focus:ring-2 focus:ring-mint/50 text-zinc-900 dark:text-white placeholder:text-zinc-400 overflow-hidden"
                    style="height: 42px;"
                    :disabled="isWaiting || isTyping"
                ></textarea>
                <button
                    type="submit"
                    class="flex-shrink-0 w-10 h-10 bg-mint text-zinc-950 rounded-xl flex items-center justify-center hover:bg-mint/90 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!inputMessage.trim() || isWaiting || isTyping"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            <p class="text-[10px] text-zinc-400 mt-2 text-center">
                AI may produce inaccurate information.
                <a href="{{ route('contact.index') }}" class="text-mint hover:underline">Contact Fatih</a> for details.
            </p>
        </div>
    </div>
</div>

@once
    @push('scripts')
        <script>
            function chatBot() {
                return {
                    inputMessage: '',
                    isTyping: false,
                    isWaiting: false,
                    typingMessageId: null,
                    typedContent: '',
                    typingInterval: null,
                    showSuggestions: true,
                    messages: @js($messages),
                    nextTempId: -1,

                    init() {
                        // Sync with Livewire messages — but merge carefully
                        this.$wire.$watch('messages', (value) => {
                            // Replace temp messages with real ones from server
                            this.messages = value;
                            this.isWaiting = false;
                            this.showSuggestions = value.length <= 2;
                            this.$nextTick(() => this.scrollToBottom());
                        });

                        // Watch for typing progress event
                        this.$wire.on('typingProgress', (event) => {
                            const { messageId, content, speed } = event[0];
                            this.isWaiting = false;
                            this.startTypingEffect(messageId, content, speed);
                        });

                        // Watch for typing skipped event
                        this.$wire.on('typingSkipped', (event) => {
                            const { messageId, content } = event[0];
                            this.typedContent = content;
                            this.stopTypingEffect();
                        });

                        // Watch for message sent event
                        this.$wire.on('messageSent', () => {
                            this.$nextTick(() => this.scrollToBottom());
                        });

                        // Listen for chat opened event
                        this.$wire.on('chatOpened', () => {
                            this.$nextTick(() => this.$refs.textarea?.focus());
                        });

                        // Initial scroll
                        this.$nextTick(() => this.scrollToBottom());

                        // Focus textarea when chat opens
                        this.$watch('$wire.isOpen', (value) => {
                            if (value) {
                                this.$nextTick(() => this.$refs.textarea?.focus());
                            }
                        });
                    },

                    startTypingEffect(messageId, fullContent, speed = 15) {
                        this.typingMessageId = messageId;
                        this.typedContent = '';
                        this.isTyping = true;

                        let index = 0;
                        const content = fullContent;

                        // Clear any existing interval
                        if (this.typingInterval) {
                            clearInterval(this.typingInterval);
                        }

                        // Adaptive speed based on content length
                        const adaptiveSpeed = content.length > 500 ? speed / 2 : speed;

                        this.typingInterval = setInterval(() => {
                            if (index < content.length) {
                                // Add multiple characters at once for longer content
                                const chunkSize = content.length > 1000 ? 3 : 1;
                                this.typedContent += content.substring(index, index + chunkSize);
                                index += chunkSize;

                                // Auto-scroll while typing
                                if (this.shouldAutoScroll()) {
                                    this.scrollToBottom();
                                }
                            } else {
                                this.stopTypingEffect();
                                this.$wire.handleTypingComplete(messageId);
                            }
                        }, adaptiveSpeed);
                    },

                    stopTypingEffect() {
                        if (this.typingInterval) {
                            clearInterval(this.typingInterval);
                            this.typingInterval = null;
                        }
                        this.isTyping = false;
                        this.typingMessageId = null;
                    },

                    skipTyping() {
                        if (this.typingMessageId) {
                            this.$wire.skipTyping();
                        }
                    },

                    submitMessage() {
                        const message = this.inputMessage.trim();
                        if (!message || this.isTyping || this.isWaiting) return;

                        // Optimistic update: show user message instantly
                        const tempId = this.nextTempId--;
                        this.messages.push({
                            id: tempId,
                            role: 'user',
                            content: message,
                            sent_at: 'just now',
                        });

                        // Show thinking indicator immediately
                        this.isWaiting = true;
                        this.showSuggestions = false;
                        this.inputMessage = '';

                        // Scroll to bottom to show the new message
                        this.$nextTick(() => this.scrollToBottom());

                        // Send to Livewire
                        this.$wire.message = message;
                        this.$wire.sendMessage();
                    },

                    sendSuggestion(text) {
                        if (this.isTyping || this.isWaiting) return;
                        this.inputMessage = text;
                        this.submitMessage();
                    },

                    scrollToBottom() {
                        const container = this.$refs.messageContainer;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    },

                    shouldAutoScroll() {
                        const container = this.$refs.messageContainer;
                        if (!container) return true;
                        const threshold = 50;
                        return container.scrollTop + container.clientHeight >= container.scrollHeight - threshold;
                    },

                    handleScroll() {
                        // Handle scroll events if needed
                    },

                    formatMarkdown(text) {
                        if (!text) return '';

                        let formatted = text;

                        // Escape HTML
                        formatted = formatted
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;');

                        // Convert URLs to links
                        formatted = formatted.replace(
                            /(https?:\/\/[^\s<]+)/g,
                            '<a href="$1" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">$1</a>'
                        );

                        // Convert **bold** to <strong>
                        formatted = formatted.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');

                        // Convert *italic* to <em>
                        formatted = formatted.replace(/\*(.+?)\*/g, '<em>$1</em>');

                        // Convert `code` to <code>
                        formatted = formatted.replace(/`(.+?)`/g, '<code class="bg-zinc-100 dark:bg-zinc-700 px-1 py-0.5 rounded text-xs">$1</code>');

                        // Convert bullet points
                        const lines = formatted.split('\n');
                        let inList = false;
                        let result = [];

                        for (const line of lines) {
                            if (line.match(/^[•\-\*]\s+(.+)$/)) {
                                if (!inList) {
                                    result.push('<ul class="list-disc pl-5 space-y-1 my-2">');
                                    inList = true;
                                }
                                result.push('<li>' + line.replace(/^[•\-\*]\s+/, '') + '</li>');
                            } else {
                                if (inList) {
                                    result.push('</ul>');
                                    inList = false;
                                }
                                result.push(line);
                            }
                        }

                        if (inList) {
                            result.push('</ul>');
                        }

                        formatted = result.join('\n');

                        // Convert newlines to <br> (but not inside lists)
                        formatted = formatted.replace(/([^<])\n([^<])/g, '$1<br>$2');

                        return formatted;
                    }
                }
            }
        </script>
    @endpush

    @push('styles')
        <style>
            [x-cloak] { display: none !important; }

            .chat-scrollbar::-webkit-scrollbar {
                width: 4px;
            }
            .chat-scrollbar::-webkit-scrollbar-track {
                background: transparent;
            }
            .chat-scrollbar::-webkit-scrollbar-thumb {
                background-color: rgba(161, 161, 170, 0.5);
                border-radius: 20px;
            }

            /* Typing cursor animation */
            @keyframes blink {
                0%, 50% { opacity: 1; }
                51%, 100% { opacity: 0; }
            }

            .typing-cursor {
                animation: blink 1s infinite;
            }

            /* Smooth message transitions */
            .message-enter {
                animation: messageSlide 0.3s ease-out;
            }

            @keyframes messageSlide {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endpush
@endonce
