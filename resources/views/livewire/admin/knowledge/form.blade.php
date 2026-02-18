<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">{{ $entryId ? 'Edit Knowledge Entry' : 'Add Knowledge Entry' }}</h1>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">
                    {{ $entryId ? 'Update existing knowledge entry' : 'Create new knowledge for AI assistant' }}
                </p>
            </div>
            <a href="{{ route('admin.knowledge.index') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m12 19-7-7 7-7"/>
                    <path d="M19 12H5"/>
                </svg>
                Back
            </a>
        </div>
    </x-slot>

    <form wire:submit="save" class="max-w-3xl space-y-6">
        <!-- Title -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <label class="block text-sm font-bold mb-2">Title <span class="text-red-500">*</span></label>
            <input type="text" wire:model="title"
                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border {{ $errors->has('title') ? 'border-red-400' : 'border-zinc-200 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors"
                   placeholder="e.g., Laravel Best Practices">
            @error('title')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Category -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <label class="block text-sm font-bold mb-2">Category <span class="text-red-500">*</span></label>
            <div class="relative">
                <input type="text" wire:model="category" list="categories"
                       class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border {{ $errors->has('category') ? 'border-red-400' : 'border-zinc-200 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors"
                       placeholder="e.g., laravel, general, skills">
                <datalist id="categories">
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">
                    @endforeach
                </datalist>
            </div>
            <p class="mt-2 text-xs text-zinc-500">Select existing or type new category</p>
            @error('category')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <label class="block text-sm font-bold mb-2">Content <span class="text-red-500">*</span></label>
            <p class="text-xs text-zinc-500 mb-2">This content will be used by AI to answer user questions</p>
            <textarea wire:model="content" rows="10"
                      class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border {{ $errors->has('content') ? 'border-red-400' : 'border-zinc-200 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors resize-none font-mono text-sm"
                      placeholder="Enter detailed knowledge content here..."></textarea>
            @error('content')
                <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tags -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <label class="block text-sm font-bold mb-2">Tags</label>
            <input type="text" wire:model="tags"
                   class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                   placeholder="laravel, php, backend (comma separated)">
            <p class="mt-2 text-xs text-zinc-500">Comma-separated tags for better searchability</p>
        </div>

        <!-- Status -->
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <label class="block text-sm font-bold">Active</label>
                    <p class="text-xs text-zinc-500">Inactive entries won't be used by AI</p>
                </div>
                <button type="button" wire:click="$toggle('is_active')"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $is_active ? 'bg-mint' : 'bg-zinc-300 dark:bg-zinc-700' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                {{ $entryId ? 'Update Entry' : 'Create Entry' }}
            </button>

            <a href="{{ route('admin.knowledge.index') }}" wire:navigate
               class="px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
