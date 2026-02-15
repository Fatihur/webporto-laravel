<div>
    <div class="mb-8">
        <a href="{{ route('admin.experiences.index') }}" wire:navigate
           class="inline-flex items-center gap-2 text-sm text-zinc-500 hover:text-mint transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 19-7-7 7-7"/>
                <path d="M19 12H5"/>
            </svg>
            Back to Experience
        </a>
        <h1 class="text-3xl font-bold mt-4">{{ $experienceId ? 'Edit Experience' : 'Add Experience' }}</h1>
    </div>

    <form wire:submit="save" class="max-w-2xl">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6 space-y-6">
            <!-- Company -->
            <div>
                <label class="block text-sm font-bold mb-2">Company</label>
                <input type="text" wire:model="company"
                       class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                       placeholder="Company name">
                @error('company')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Role -->
            <div>
                <label class="block text-sm font-bold mb-2">Role / Position</label>
                <input type="text" wire:model="role"
                       class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                       placeholder="Job title">
                @error('role')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold mb-2">Description</label>
                <textarea wire:model="description" rows="4"
                          class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors resize-none"
                          placeholder="Job description, responsibilities, achievements..."></textarea>
                @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Dates -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-bold mb-2">Start Date</label>
                    <input type="date" wire:model="start_date"
                           class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                    @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold mb-2">End Date</label>
                    <input type="date" wire:model="end_date"
                           class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors"
                           {{ $is_current ? 'disabled' : '' }}>
                    @error('end_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Current Position -->
            <div class="flex items-center gap-3">
                <input type="checkbox" wire:model.live="is_current" id="is_current"
                       class="w-5 h-5 rounded border-zinc-300 text-mint focus:ring-mint">
                <label for="is_current" class="font-bold cursor-pointer">I currently work here</label>
            </div>

            <!-- Order -->
            <div>
                <label class="block text-sm font-bold mb-2">Display Order</label>
                <input type="number" wire:model="order" min="0"
                       class="w-32 px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors">
                <p class="text-xs text-zinc-500 mt-1">Lower numbers appear first</p>
                @error('order')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <button type="submit"
                        class="px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    {{ $experienceId ? 'Update' : 'Save' }}
                </button>

                <a href="{{ route('admin.experiences.index') }}" wire:navigate
                   class="px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:border-zinc-400 transition-colors">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
