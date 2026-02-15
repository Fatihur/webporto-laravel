<div>
    @if($successMessage)
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 dark:text-green-400 shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <div>
                <p class="font-bold text-green-800 dark:text-green-200">Success!</p>
                <p class="text-sm text-green-700 dark:text-green-300">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-6">
        <!-- Name Field -->
        <div>
            <label for="name" class="block text-sm font-bold mb-2">Name</label>
            <input
                type="text"
                id="name"
                wire:model.live="name"
                class="w-full px-5 py-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('name') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors"
                placeholder="Your name"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Email Field -->
        <div>
            <label for="email" class="block text-sm font-bold mb-2">Email</label>
            <input
                type="email"
                id="email"
                wire:model.live="email"
                class="w-full px-5 py-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('email') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors"
                placeholder="your@email.com"
            >
            @error('email')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Project Type Field -->
        <div>
            <label for="project_type" class="block text-sm font-bold mb-2">Project Type</label>
            <select
                id="project_type"
                wire:model.live="project_type"
                class="w-full px-5 py-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('project_type') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors appearance-none cursor-pointer"
            >
                <option value="">Select project type</option>
                @foreach($projectTypes as $typeKey => $typeValue)
                    <option value="{{ $typeKey }}">{{ $typeValue }}</option>
                @endforeach
            </select>
            @error('project_type')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Message Field -->
        <div>
            <label for="message" class="block text-sm font-bold mb-2">Message</label>
            <textarea
                id="message"
                wire:model.live="message"
                rows="5"
                class="w-full px-5 py-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('message') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors resize-none"
                placeholder="Tell me about your project..."
            ></textarea>
            @error('message')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-70 cursor-not-allowed"
            class="w-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-black py-4 rounded-2xl hover:scale-[1.02] transition-all flex items-center justify-center gap-3"
        >
            <span wire:loading.remove>Send Message</span>
            <span wire:loading>Sending...</span>
            <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/></svg>
            <svg wire:loading class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
        </button>
    </form>
</div>
