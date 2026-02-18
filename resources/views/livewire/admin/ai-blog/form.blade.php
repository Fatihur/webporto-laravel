<div>
    <div class="mb-8">
        <a href="{{ route('admin.ai-blog.index') }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm text-zinc-500 hover:text-mint transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Automations
        </a>
        <h1 class="text-3xl font-bold mt-4">{{ $automationId ? 'Edit Automation' : 'New Automation' }}</h1>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Configuration -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Configuration</h2>

                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Automation Name</label>
                            <input type="text" wire:model="name"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="e.g., Daily Tech Insights">
                            @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Topic Prompt -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Topic Prompt</label>
                            <p class="text-xs text-zinc-500 mb-2">Describe the topic or theme for AI to generate articles about.</p>
                            <textarea wire:model="topic_prompt" rows="4"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="e.g., Write about latest trends in web development, focusing on Laravel, React, and modern frontend technologies.">{{ $topic_prompt }}</textarea>
                            @error('topic_prompt')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Content Prompt -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Content Guidelines</label>
                            <p class="text-xs text-zinc-500 mb-2">Additional instructions for content style, tone, or specific requirements.</p>
                            <textarea wire:model="content_prompt" rows="4"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="e.g., Write in Indonesian language with friendly and professional tone. Include practical examples and actionable tips.">{{ $content_prompt }}</textarea>
                            @error('content_prompt')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Content Angles -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Content Angles (Pilih Minimal 1)</label>
                            <p class="text-xs text-zinc-500 mb-3">Sistem akan rotate angle untuk setiap artikel agar variatif.</p>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($availableContentAngles as $key => $label)
                                    <label class="flex items-center gap-2 p-3 rounded-xl border border-zinc-200 dark:border-zinc-800 cursor-pointer hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors {{ in_array($key, $content_angles) ? 'bg-mint/10 border-mint dark:bg-mint/10 dark:border-mint' : '' }}">
                                        <input type="checkbox" wire:model="content_angles" value="{{ $key }}"
                                               class="w-4 h-4 rounded border-zinc-300 text-mint focus:ring-mint">
                                        <span class="text-sm">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('content_angles')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @error('content_angles.*')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Prompt Tips -->
                <div class="bg-violet/10 dark:bg-violet/5 rounded-2xl border border-violet/20 p-6">
                    <h3 class="text-sm font-bold text-violet mb-2 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        Prompt Tips
                    </h3>
                    <ul class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1 list-disc list-inside">
                        <li>Be specific about the topic you want covered</li>
                        <li>Mention target audience (beginners, advanced, etc.)</li>
                        <li>Specify desired article length or depth</li>
                        <li>Include any specific points that should be covered</li>
                        <li>Mention writing style (formal, casual, tutorial, etc.)</li>
                    </ul>
                </div>
            </div>

            <!-- Right Column - Settings -->
            <div class="space-y-6">
                <!-- Actions -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Actions</h2>

                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                                <polyline points="17 21 17 13 7 13 7 21"/>
                                <polyline points="7 3 7 8 15 8"/>
                            </svg>
                            {{ $automationId ? 'Update Automation' : 'Create Automation' }}
                        </button>

                        <button type="button" wire:click="testPrompt"
                                class="w-full px-6 py-3 bg-violet/10 text-violet rounded-xl font-bold hover:bg-violet/20 transition-colors flex items-center justify-center gap-2"
                                wire:loading.attr="disabled"
                                wire:target="testPrompt">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
                            </svg>
                            <span wire:loading.remove wire:target="testPrompt">Test Prompt</span>
                            <span wire:loading wire:target="testPrompt">Testing...</span>
                        </button>

                        <a href="{{ route('admin.ai-blog.index') }}" wire:navigate
                           class="w-full px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:border-zinc-400 transition-colors flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Schedule Settings -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Schedule</h2>

                    <div class="space-y-4">
                        <!-- Frequency -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Frequency</label>
                            <select wire:model="frequency"
                                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                                @foreach($frequencies as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('frequency')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Scheduled Time -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Run At (Time)</label>
                            <input type="time" wire:model="scheduled_at"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                            @error('scheduled_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Max Articles Per Day -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Max Articles/Day</label>
                            <input type="number" wire:model="max_articles_per_day" min="1" max="10"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                            @error('max_articles_per_day')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Category & Publishing -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Publishing</h2>

                    <div class="space-y-4">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Category</label>
                            <select wire:model="category"
                                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Image URL -->
                        <div>
                            <label class="block text-sm font-bold mb-2">
                                Thumbnail Image URL
                                <span class="text-xs font-normal text-zinc-400 ml-1">(Optional)</span>
                            </label>
                            <input type="url" wire:model="image_url"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="https://example.com/image.jpg">
                            @error('image_url')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500 mt-1">Leave empty to use default category image</p>

                            <!-- Image Preview -->
                            @if($image_url)
                                <div class="mt-3 aspect-video rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                    <img src="{{ $image_url }}" alt="Preview" class="w-full h-full object-cover"
                                         onerror="this.src='{{ asset('images/placeholder.jpg') }}'; this.classList.add('opacity-50');">
                                </div>
                            @endif
                        </div>

                        <!-- Auto Publish -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" wire:model="auto_publish" id="auto_publish"
                                       class="w-5 h-5 rounded border-zinc-300 text-mint focus:ring-mint">
                                <label for="auto_publish" class="font-bold cursor-pointer">Auto Publish</label>
                            </div>
                            <span class="text-sm {{ $auto_publish ? 'text-green-600' : 'text-zinc-500' }}">
                                {{ $auto_publish ? 'Will publish' : 'Draft' }}
                            </span>
                        </div>

                        <!-- Active Status -->
                        <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" wire:model="is_active" id="is_active"
                                       class="w-5 h-5 rounded border-zinc-300 text-mint focus:ring-mint">
                                <label for="is_active" class="font-bold cursor-pointer">Active</label>
                            </div>
                            <span class="text-sm {{ $is_active ? 'text-green-600' : 'text-zinc-500' }}">
                                {{ $is_active ? 'Running' : 'Paused' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Test Prompt Modal -->
    <div x-show="$wire.showTestModal"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
         wire:click.self="closeTestModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-zinc-200 dark:border-zinc-800">
                    <h3 class="text-xl font-bold">Test Prompt Result</h3>
                    <button type="button" wire:click="closeTestModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"/>
                            <path d="m6 6 12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto flex-1">
                    @if($isTesting)
                        <div class="flex flex-col items-center justify-center py-12" wire:key="testing-state">
                            <div class="w-12 h-12 border-4 border-violet/20 border-t-violet rounded-full animate-spin mb-4"></div>
                            <p class="text-zinc-500">Generating article with AI...</p>
                            <p class="text-sm text-zinc-400 mt-2">This may take a few moments</p>
                        </div>
                    @elseif($testError)
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6" wire:key="error-state">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-red-500">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="15" x2="9" y1="9" y2="15"/>
                                    <line x1="9" x2="15" y1="9" y2="15"/>
                                </svg>
                                <h4 class="font-bold text-red-600">Generation Failed</h4>
                            </div>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $testError }}</p>
                        </div>
                    @elseif($testResult)
                        <div class="space-y-6" wire:key="result-state-{{ md5($testResult['title'] ?? time()) }}">
                            <!-- Title -->
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Title</label>
                                <h4 class="text-xl font-bold mt-1">{{ $testResult['title'] ?? 'No title' }}</h4>
                            </div>

                            <!-- Meta Info -->
                            <div class="grid grid-cols-2 gap-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl">
                                <div>
                                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Meta Title</label>
                                    <p class="text-sm mt-1">{{ $testResult['meta_title'] ?? '-' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Read Time</label>
                                    <p class="text-sm mt-1">{{ $testResult['estimated_read_time'] ?? '5' }} minutes</p>
                                </div>
                                <div class="col-span-2">
                                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Meta Description</label>
                                    <p class="text-sm mt-1 text-zinc-600 dark:text-zinc-400">{{ $testResult['meta_description'] ?? '-' }}</p>
                                </div>
                            </div>

                            <!-- Thumbnail Image -->
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Thumbnail Image</label>
                                <div class="mt-2 space-y-3">
                                    <input type="url" wire:model="image_url"
                                           class="w-full px-4 py-2 rounded-xl bg-white dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                           placeholder="https://example.com/image.jpg">
                                    @if($image_url)
                                        <div class="aspect-video rounded-xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 max-h-48">
                                            <img src="{{ $image_url }}" alt="Preview" class="w-full h-full object-cover"
                                                 onerror="this.src='{{ asset('images/placeholder.jpg') }}'; this.classList.add('opacity-50');">
                                        </div>
                                    @else
                                        <p class="text-sm text-zinc-500">Default category image will be used</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Excerpt</label>
                                <div class="mt-2 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl text-sm text-zinc-600 dark:text-zinc-400">
                                    {{ $testResult['excerpt'] ?? 'No excerpt' }}
                                </div>
                            </div>

                            <!-- Content Preview -->
                            <div>
                                <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Content Preview</label>
                                <div class="mt-2 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl prose dark:prose-invert max-w-none max-h-64 overflow-y-auto">
                                    {{ strip_tags($testResult['excerpt'] ?? 'No content preview available') }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-zinc-200 dark:border-zinc-800">
                    <button type="button" wire:click="closeTestModal" class="px-6 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:border-zinc-400 transition-colors">
                        Close
                    </button>
                    @if($testResult && !$isTesting)
                        <div class="flex items-center gap-3" wire:key="footer-actions-{{ md5($testResult['title'] ?? 'empty') }}">
                            <button type="button" wire:click="testPrompt" class="px-6 py-2 bg-violet/10 text-violet rounded-xl font-bold hover:bg-violet/20 transition-colors">
                                Regenerate
                            </button>
                            <button type="button" wire:click="testPublish" wire:key="publish-btn" class="px-6 py-2 bg-mint text-zinc-950 rounded-xl font-bold hover:bg-mint/80 transition-colors flex items-center gap-2" wire:loading.attr="disabled" wire:target="testPublish">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M5 12h14"/>
                                    <path d="m12 5 7 7-7 7"/>
                                </svg>
                                <span wire:loading.remove wire:target="testPublish">Publish Now</span>
                                <span wire:loading wire:target="testPublish">Publishing...</span>
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
