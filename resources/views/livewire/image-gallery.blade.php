<div>
    <!-- Upload Area -->
    <div class="mb-6">
        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
            @if ($isUploading)
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center rounded-lg z-10">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-4 border-t-blue-500 border-t-transparent mx-auto mb-2"></div>
                        <span class="text-white text-sm">{{ __('Uploading...') }}</span>
                    </div>
                </div>
            @endif
            
            <label class="cursor-pointer block">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="mt-2 block text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Click to upload or drag and drop') }}
                </span>
                <span class="text-xs text-gray-500">{{ __('PNG, JPG, GIF up to 10MB') }}</span>
                <input
                    type="file"
                    wire:model="upload"
                    accept="image/*"
                    class="hidden"
                >
            </label>
        </div>
        
        <!-- Progress Bar -->
        @if ($uploadProgress > 0 && $uploadProgress < 100)
            <div class="mt-4">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-1">
                    <span>{{ __('Uploading...') }}</span>
                    <span>{{ $uploadProgress }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                        style="width: {{ $uploadProgress }}%"
                    ></div>
                </div>
            </div>
        @endif
    </div>

    <!-- Error Message -->
    @if (session()->has('error'))
        <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 border border-red-400 text-red-700 dark:text-red-300 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 border border-green-400 text-green-700 dark:text-green-300 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Images Grid -->
    @if ($images->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($images as $image)
                <div 
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-shadow group relative"
                    wire:sortable
                    wire:sortable.handle=".drag-handle"
                    wire:sortable.item="{{ $image->id }}"
                    wire:sortable.target="{ id: {{ $image->id }}, order: {{ $image->sort_order }}}"
                >
                    <!-- Drag Handle -->
                    <div class="drag-handle absolute top-2 left-2 cursor-move opacity-0 group-hover:opacity-100 transition-opacity z-10">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                        </svg>
                    </div>
                    
                    <!-- Image Preview -->
                    <div class="aspect-square overflow-hidden rounded-t-lg bg-gray-100 dark:bg-gray-900">
                        <img 
                            src="{{ $image->image_path }}" 
                            alt="{{ $image->alt_text ?? '' }}"
                            class="w-full h-full object-cover"
                            loading="lazy"
                        >
                    </div>
                    
                    <!-- Image Info -->
                    <div class="p-3">
                        @if ($editingImageId === $image->id)
                            <!-- Edit Mode -->
                            <div class="space-y-2">
                                <input
                                    type="text"
                                    wire:model="editingTitle"
                                    placeholder="{{ __('Title') }}"
                                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                >
                                <input
                                    type="text"
                                    wire:model="editingAltText"
                                    placeholder="{{ __('Alt Text') }}"
                                    class="w-full text-sm border border-gray-300 dark:border-gray-600 rounded px-2 py-1 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                >
                                <div class="flex gap-2">
                                    <button
                                        wire:click="saveEdit({{ $image->id }})"
                                        class="flex-1 px-2 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700"
                                    >
                                        {{ __('Save') }}
                                    </button>
                                    <button
                                        wire:click="cancelEdit"
                                        class="flex-1 px-2 py-1 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm rounded hover:bg-gray-400"
                                    >
                                        {{ __('Cancel') }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- View Mode -->
                            <p class="text-sm text-gray-700 dark:text-gray-300 truncate" title="{{ $image->title }}">
                                {{ $image->title ?? __('Untitled') }}
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ $image->alt_text ?? '' }}</p>
                            
                            <!-- Actions -->
                            <div class="flex gap-2 mt-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    wire:click="startEdit({{ $image->id }})"
                                    class="flex-1 px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600"
                                >
                                    {{ __('Edit') }}
                                </button>
                                <button
                                    wire:click="deleteImage({{ $image->id }})"
                                    wire:confirm="{{ __('Are you sure you want to delete this image?') }}"
                                    class="flex-1 px-2 py-1 text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded hover:bg-red-200 dark:hover:bg-red-900/50"
                                >
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('No images uploaded yet') }}</p>
            <p class="text-sm text-gray-500">{{ __('Upload your first image using the area above') }}</p>
        </div>
    @endif

    <!-- Pagination -->
    @if ($images->hasPages())
        <div class="mt-6">
            {{ $images->links() }}
        </div>
    @endif
</div>
