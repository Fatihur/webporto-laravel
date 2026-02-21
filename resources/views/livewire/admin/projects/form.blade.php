<div>
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('admin.projects.index') }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm text-zinc-500 hover:text-mint transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Projects
        </a>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-4">
            <h1 class="text-2xl sm:text-3xl font-bold">{{ $projectId ? 'Edit Project' : 'Add Project' }}</h1>

            @if($projectId)
                <a href="{{ route('projects.show', $slug) }}" target="_blank"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-mint text-zinc-950 rounded-xl font-bold hover:bg-mint/80 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        <polyline points="15 3 21 3 21 9"/>
                        <line x1="10" y1="14" x2="21" y2="3"/>
                    </svg>
                    Preview
                </a>
            @endif
        </div>
    </div>

    <form wire:submit="save" class="space-y-4 sm:space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                    <h2 class="text-lg font-bold mb-4">Basic Information</h2>

                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Title</label>
                            <input type="text" wire:model.live="title"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="Project title">
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Slug</label>
                            <input type="text" wire:model="slug"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="project-slug">
                            @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

<!-- Description -->
<div x-data="{
    init() {
        const isDark = document.documentElement.classList.contains('dark');
        $(this.$refs.descriptionEditor).summernote({
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ],
            callbacks: {
                onChange: (contents) => {
                    @this.set('description', contents);
                }
            }
        });
        if (isDark) {
            $(this.$refs.descriptionEditor).next('.note-editor').addClass('dark');
        }
    }
}" wire:ignore>
                            <label class="block text-sm font-bold mb-2">Description</label>
                            <textarea x-ref="descriptionEditor" rows="3"
                                      class="w-full rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                      placeholder="Brief description of the project">{{ $description }}</textarea>
                            @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

<!-- Content -->
<div x-data="{
    init() {
        const isDark = document.documentElement.classList.contains('dark');
        $(this.$refs.contentEditor).summernote({
            height: 250,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ],
            callbacks: {
                onChange: (contents) => {
                    @this.set('content', contents);
                },
                onFullscreenEnter: function() {
                    document.body.classList.add('summernote-fullscreen');
                    if (document.documentElement.classList.contains('dark')) {
                        $('.note-editor-fullscreen').addClass('dark');
                    }
                },
                onFullscreenExit: function() {
                    document.body.classList.remove('summernote-fullscreen');
                }
            }
        });
        if (isDark) {
            $(this.$refs.contentEditor).next('.note-editor').addClass('dark');
        }
    }
}" wire:ignore>
                            <label class="block text-sm font-bold mb-2">Content</label>
                            <p class="text-xs text-zinc-500 mb-2">Supports: Code blocks, LaTeX math ($E=mc^2$ or $$...$$)</p>
                            <textarea x-ref="contentEditor" rows="10"
                                      class="w-full rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                      placeholder="Detailed project content">{{ $content }}</textarea>
                            @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Case Study -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Interactive Case Study</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Problem</label>
                            <textarea wire:model="case_study_problem" rows="4"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="Masalah utama yang ingin diselesaikan pada project ini."></textarea>
                            @error('case_study_problem')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Process</label>
                            <textarea wire:model="case_study_process" rows="4"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="Jelaskan pendekatan, strategi, dan langkah implementasi."></textarea>
                            @error('case_study_process')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Result</label>
                            <textarea wire:model="case_study_result" rows="4"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="Dampak hasil (business impact, user impact, technical outcome)."></textarea>
                            @error('case_study_result')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">Project Stats</h2>
                        <button type="button" wire:click="addStat"
                                class="text-sm font-bold text-mint hover:text-mint/80 transition-colors">
                            + Add Stat
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($stats as $index => $stat)
                            <div class="flex gap-3" wire:key="stat-{{ $index }}">
                                <input type="text" wire:model="stats.{{ $index }}.label"
                                       class="flex-1 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                       placeholder="Label (e.g., Client)">
                                <input type="text" wire:model="stats.{{ $index }}.value"
                                       class="flex-1 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                       placeholder="Value (e.g., Acme Corp)">
                                <button type="button" wire:click="removeStat({{ $index }})"
                                        class="px-3 py-2 text-red-500 hover:text-red-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    @error('stats')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Case Study Metrics -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold">Case Study Metrics</h2>
                        <button type="button" wire:click="addCaseStudyMetric"
                                class="text-sm font-bold text-mint hover:text-mint/80 transition-colors">
                            + Add Metric
                        </button>
                    </div>

                    <div class="space-y-3">
                        @foreach($case_study_metrics as $index => $metric)
                            <div class="flex gap-3" wire:key="case-study-metric-{{ $index }}">
                                <input type="text" wire:model="case_study_metrics.{{ $index }}.label"
                                       class="flex-1 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                       placeholder="Label (e.g., Conversion Rate)">
                                <input type="text" wire:model="case_study_metrics.{{ $index }}.value"
                                       class="flex-1 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                       placeholder="Value (e.g., +37%)">
                                <button type="button" wire:click="removeCaseStudyMetric({{ $index }})"
                                        class="px-3 py-2 text-red-500 hover:text-red-600 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                    @error('case_study_metrics')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Gallery -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Gallery</h2>

                    <!-- Existing Gallery -->
                    @if(count($existingGallery) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                            @foreach($existingGallery as $index => $image)
                                <div class="relative aspect-square rounded-xl overflow-hidden group"
                                     wire:key="existing-gallery-{{ $index }}">
                                    <img src="{{ Storage::url($image) }}" alt=""
                                         class="w-full h-full object-cover">
                                    <button type="button" wire:click="removeGalleryImage({{ $index }})"
                                            class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- New Gallery Images Preview -->
                    @if(count($gallery) > 0)
                        <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-4">
                            @foreach($gallery as $index => $image)
                                <div class="relative aspect-square rounded-xl overflow-hidden group border-2 border-mint"
                                     wire:key="new-gallery-{{ $index }}">
                                    <img src="{{ $image->temporaryUrl() }}" alt=""
                                         class="w-full h-full object-cover">
                                    <div class="absolute top-1 right-1 bg-mint text-zinc-950 text-[10px] font-bold px-2 py-0.5 rounded-full">NEW</div>
                                    <button type="button" wire:click="removeNewGalleryImage({{ $index }})"
                                            class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                             viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 6h18"/>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- New Gallery Upload -->
                    <div class="relative border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-6 text-center hover:border-mint transition-colors cursor-pointer overflow-hidden"
                         x-data="{ dragging: false }"
                         x-on:dragover.prevent="dragging = true"
                         x-on:dragleave.prevent="dragging = false"
                         x-on:drop.prevent="dragging = false"
                         x-bind:class="{ 'border-mint bg-mint/5': dragging }">
                        <input type="file" wire:model="gallery" multiple accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="mx-auto mb-2 text-zinc-400">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <p class="text-sm text-zinc-500">Click or drag images here</p>
                        <p class="text-xs text-zinc-400 mt-1">PNG, JPG up to 2MB each</p>
                    </div>
                    <div wire:loading wire:target="gallery" class="text-sm text-mint mt-2">Uploading...</div>
                    @error('gallery.*')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Right Column - Settings -->
            <div class="space-y-4 sm:space-y-6">
                <!-- Publish Actions -->
                <div class="bg-white dark:bg-zinc-900 rounded-xl sm:rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
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
                            {{ $projectId ? 'Update Project' : 'Create Project' }}
                        </button>

                        <!-- Translate Button -->
                        <button type="button"
                                wire:click="translateToEnglish"
                                wire:loading.attr="disabled"
                                wire:target="translateToEnglish"
                                class="w-full px-6 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round"
                                 wire:loading.remove
                                 wire:target="translateToEnglish">
                                <path d="m5 8 6 6"/>
                                <path d="m4 14 6-6 2-3"/>
                                <path d="M2 5h12"/>
                                <path d="M7 2h1"/>
                                <path d="m22 22-5-10-5 10"/>
                                <path d="M14 18h6"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round"
                                 class="animate-spin"
                                 wire:loading
                                 wire:target="translateToEnglish">
                                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
                            </svg>
                            <span wire:loading.remove wire:target="translateToEnglish">Translate to English</span>
                            <span wire:loading wire:target="translateToEnglish">Translating...</span>
                        </button>

                        <a href="{{ route('admin.projects.index') }}" wire:navigate
                           class="w-full px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:border-zinc-400 transition-colors flex items-center justify-center">
                            Cancel
                        </a>
                    </div>

                    <p class="text-xs text-zinc-500 mt-3 text-center">
                        Translate title, description, and content to English using AI
                    </p>
                </div>

                <!-- Category & Date -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Details</h2>

                    <div class="space-y-4">
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Category</label>
                            <select wire:model="category"
                                    class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                                <option value="">Select category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Project Date -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Project Date</label>
                            <input type="date" wire:model="project_date"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                            @error('project_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Project Link -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Project Link</label>
                            <input type="url" wire:model="link"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="https://example.com">
                            <p class="text-xs text-zinc-500 mt-1">Link to live project (optional)</p>
                            @error('link')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Featured -->
                        <div class="flex items-center gap-3 pt-2">
                            <input type="checkbox" wire:model="is_featured" id="is_featured"
                                   class="w-5 h-5 rounded border-zinc-300 text-mint focus:ring-mint">
                            <label for="is_featured" class="font-bold cursor-pointer">Featured Project</label>
                        </div>
                    </div>
                </div>

                <!-- Thumbnail -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Thumbnail</h2>

                    <!-- Preview -->
                    @if($thumbnailPreview)
                        <div class="relative aspect-video rounded-xl overflow-hidden mb-4 group">
                            <img src="{{ $thumbnailPreview }}" alt="" class="w-full h-full object-cover">
                            @if($thumbnail)
                                <div class="absolute top-2 right-2 bg-mint text-zinc-950 text-[10px] font-bold px-2 py-0.5 rounded-full">NEW</div>
                            @endif
                            <button type="button" wire:click="removeNewThumbnail"
                                    class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                     fill="none" stroke="white" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    <div
                        class="relative border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-4 text-center hover:border-mint transition-colors cursor-pointer overflow-hidden">
                        <input type="file" wire:model="thumbnail" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="mx-auto mb-2 text-zinc-400">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <p class="text-sm text-zinc-500">{{ $thumbnailPreview ? 'Change' : 'Upload' }} thumbnail</p>
                    </div>
                    <div wire:loading wire:target="thumbnail" class="text-sm text-mint mt-2">Uploading...</div>
                    @error('thumbnail')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- SEO Meta -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">SEO Meta</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold mb-2">Meta Title</label>
                            <input type="text" wire:model="meta_title"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="SEO title (optional)">
                            @error('meta_title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500 mt-1">{{ strlen((string) $meta_title) }}/255 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Meta Description</label>
                            <textarea wire:model="meta_description" rows="3"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="SEO description (optional)">{{ $meta_description }}</textarea>
                            @error('meta_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500 mt-1">{{ strlen((string) $meta_description) }}/500 characters</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2">Meta Keywords</label>
                            <input type="text" wire:model="meta_keywords"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="keyword1, keyword2, keyword3">
                            @error('meta_keywords')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500 mt-1">Comma-separated keywords</p>
                        </div>
                    </div>
                </div>

                <!-- Tags -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Tags</h2>

                    <div class="space-y-3">
                        <!-- Tag Input -->
                        <div x-data="{ newTag: '' }" x-on:keydown.enter.prevent="$wire.addTag(newTag); newTag = ''">
                            <div class="flex gap-2">
                                <input type="text" x-model="newTag"
                                       class="flex-1 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                       placeholder="Add tag...">
                                <button type="button" x-on:click="$wire.addTag(newTag); newTag = ''"
                                        class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-xl font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Tag List -->
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $index => $tag)
                                <span wire:key="tag-{{ $index }}"
                                      class="inline-flex items-center gap-1 px-3 py-1 bg-mint/10 text-mint text-sm rounded-full">
                                    {{ $tag }}
                                    <button type="button" wire:click="removeTag({{ $index }})"
                                            class="hover:text-mint/70">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"/>
                                            <path d="m6 6 12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @error('tags')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- Tech Stack -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Tech Stack</h2>

                    <div class="space-y-3">
                        <!-- Tech Input -->
                        <div x-data="{ newTech: '' }"
                             x-on:keydown.enter.prevent="$wire.addTag(newTech, 'tech_stack'); newTech = ''">
                            <div class="flex gap-2">
                                <input type="text" x-model="newTech"
                                       class="flex-1 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm"
                                       placeholder="Add technology...">
                                <button type="button"
                                        x-on:click="$wire.addTag(newTech, 'tech_stack'); newTech = ''"
                                        class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 rounded-xl font-bold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                    +
                                </button>
                            </div>
                        </div>

                        <!-- Tech List -->
                        <div class="flex flex-wrap gap-2">
                            @foreach($tech_stack as $index => $tech)
                                <span wire:key="tech-{{ $index }}"
                                      class="inline-flex items-center gap-1 px-3 py-1 bg-violet/10 text-violet text-sm rounded-full">
                                    {{ $tech }}
                                    <button type="button" wire:click="removeTag({{ $index }}, 'tech_stack')"
                                            class="hover:text-violet/70">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                             viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M18 6 6 18"/>
                                            <path d="m6 6 12 12"/>
                                        </svg>
                                    </button>
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @error('tech_stack')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>
    </form>
</div>
