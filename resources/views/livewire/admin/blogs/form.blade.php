<div>
    <div class="mb-8">
        <a href="{{ route('admin.blogs.index') }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm text-zinc-500 hover:text-mint transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Blog Posts
        </a>
        <div class="flex items-center justify-between mt-4">
            <h1 class="text-3xl font-bold">{{ $blogId ? 'Edit Blog Post' : 'New Blog Post' }}</h1>

            @if($blogId)
                <a href="{{ route('blog.show', $slug) }}" target="_blank"
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

    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Content</h2>

                    <div class="space-y-4">
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Title</label>
                            <input type="text" wire:model.live="title"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="Post title">
                            @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Slug</label>
                            <input type="text" wire:model="slug"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="post-slug">
                            @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

<!-- Excerpt -->
<div x-data="{
    init() {
        const isDark = document.documentElement.classList.contains('dark');
        $(this.$refs.excerptEditor).summernote({
            height: 120,
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol']],
                ['insert', ['link']]
            ],
            callbacks: {
                onChange: (contents) => {
                    @this.set('excerpt', contents);
                }
            }
        });
        $(this.$refs.excerptEditor).summernote('code', {{ json_encode($excerpt) }});
        if (isDark) {
            $(this.$refs.excerptEditor).next('.note-editor').addClass('dark');
        }
    }
}" wire:ignore>
                            <label class="block text-sm font-bold mb-2">Excerpt</label>
                            <textarea x-ref="excerptEditor" rows="3"
                                      class="w-full rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                      placeholder="Brief summary of the post">{{ $excerpt }}</textarea>
                            <p class="text-xs text-zinc-500 mt-1">{{ strlen(strip_tags($excerpt)) }}/500 characters</p>
                            @error('excerpt')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

<!-- Content -->
<div x-data="{
    init() {
        const isDark = document.documentElement.classList.contains('dark');
        $(this.$refs.contentEditor).summernote({
            height: 300,
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
        $(this.$refs.contentEditor).summernote('code', {{ json_encode($content) }});
        if (isDark) {
            $(this.$refs.contentEditor).next('.note-editor').addClass('dark');
        }
    }
}" wire:ignore>
                            <label class="block text-sm font-bold mb-2">Content</label>
                            <p class="text-xs text-zinc-500 mb-2">Supports: Code blocks, LaTeX math ($E=mc^2$ or $$...$$)</p>
                            <textarea x-ref="contentEditor" rows="15"
                                      class="w-full rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                      placeholder="Write your blog post content here">{{ $content }}</textarea>
                            @error('content')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Settings -->
            <div class="space-y-6">
                <!-- Publish Actions -->
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
                            {{ $blogId ? 'Update Post' : 'Create Post' }}
                        </button>

                        <a href="{{ route('admin.blogs.index') }}" wire:navigate
                           class="w-full px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:border-zinc-400 transition-colors flex items-center justify-center">
                            Cancel
                        </a>
                    </div>
                </div>

                <!-- Publishing Settings -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Publishing</h2>

                    <div class="space-y-4">
                        <!-- Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" wire:model="is_published" id="is_published"
                                       class="w-5 h-5 rounded border-zinc-300 text-mint focus:ring-mint">
                                <label for="is_published" class="font-bold cursor-pointer">Publish Now</label>
                            </div>
                            <span class="text-sm {{ $is_published ? 'text-green-600' : 'text-zinc-500' }}">
                                {{ $is_published ? 'Will be published' : 'Draft' }}
                            </span>
                        </div>

                        <!-- Published Date -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Publish Date</label>
                            <input type="date" wire:model="published_at"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   {{ !$is_published ? 'disabled' : '' }}>
                            @error('published_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Details -->
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

                        <!-- Author -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Author</label>
                            <input type="text" wire:model="author"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="Author name">
                            @error('author')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Read Time -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Read Time (minutes)</label>
                            <input type="number" wire:model="read_time" min="1" max="120"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                            @error('read_time')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">Featured Image</h2>

                    <!-- Preview -->
                    @if($imagePreview)
                        <div class="relative aspect-video rounded-xl overflow-hidden mb-4 group">
                            <img src="{{ $imagePreview }}" alt="" class="w-full h-full object-cover">
                            @if($image)
                                <div class="absolute top-2 right-2 bg-mint text-zinc-950 text-[10px] font-bold px-2 py-0.5 rounded-full">NEW</div>
                            @endif
                            <button type="button" wire:click="removeNewImage"
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
                        <input type="file" wire:model="image" accept="image/*"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="mx-auto mb-2 text-zinc-400">
                            <rect width="18" height="18" x="3" y="3" rx="2" ry="2"/>
                            <circle cx="9" cy="9" r="2"/>
                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                        </svg>
                        <p class="text-sm text-zinc-500">{{ $imagePreview ? 'Change' : 'Upload' }} image</p>
                    </div>
                    <div wire:loading wire:target="image" class="text-sm text-mint mt-2">Uploading...</div>
                    @error('image')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
                </div>

                <!-- SEO Meta -->
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
                    <h2 class="text-lg font-bold mb-4">SEO Meta</h2>

                    <div class="space-y-4">
                        <!-- Meta Title -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Meta Title</label>
                            <input type="text" wire:model="meta_title"
                                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                                   placeholder="SEO title (optional)">
                            @error('meta_title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500 mt-1">{{ strlen($meta_title) }}/255 characters</p>
                        </div>

                        <!-- Meta Description -->
                        <div>
                            <label class="block text-sm font-bold mb-2">Meta Description</label>
                            <textarea wire:model="meta_description" rows="3"
                                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                                      placeholder="SEO description (optional)">{{ $meta_description }}</textarea>
                            @error('meta_description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-xs text-zinc-500 mt-1">{{ strlen($meta_description) }}/500 characters</p>
                        </div>

                        <!-- Meta Keywords -->
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
            </div>
        </div>
    </form>
</div>
