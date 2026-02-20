<div>
    @if($successMessage)
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl flex items-start gap-3 transition-all duration-500 animate-in fade-in slide-in-from-top-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600 dark:text-green-400 shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
            <div>
                <p class="font-bold text-green-800 dark:text-green-200">Success!</p>
                <p class="text-sm text-green-700 dark:text-green-300">{{ $successMessage }}</p>
            </div>
        </div>
    @endif

    <form wire:submit="submit" class="space-y-4 md:space-y-6" x-data="{ appear: false }" x-init="setTimeout(() => appear = true, 400)">
        <!-- Name Field -->
        <div class="transition-all duration-700 transform" x-bind:class="appear ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <label for="name" class="block text-sm font-bold mb-2">Name</label>
            <input
                type="text"
                id="name"
                wire:model.live="name"
                class="w-full px-4 md:px-5 py-3.5 md:py-4 rounded-xl md:rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('name') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors text-base"
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
        <div class="transition-all duration-700 delay-100 transform" x-bind:class="appear ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <label for="email" class="block text-sm font-bold mb-2">Email</label>
            <input
                type="email"
                id="email"
                wire:model.live="email"
                class="w-full px-4 md:px-5 py-3.5 md:py-4 rounded-xl md:rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('email') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors text-base"
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
        <div class="transition-all duration-700 delay-200 transform" x-bind:class="appear ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <label for="project_type" class="block text-sm font-bold mb-2">Project Type</label>
            <div class="relative">
                <select
                    id="project_type"
                    wire:model.live="project_type"
                    class="w-full px-4 md:px-5 py-3.5 md:py-4 rounded-xl md:rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('project_type') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors appearance-none cursor-pointer text-base pr-12"
                >
                    <option value="">Select project type</option>
                    @foreach($projectTypes as $typeKey => $typeValue)
                        <option value="{{ $typeKey }}">{{ $typeValue }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500 dark:text-zinc-400">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </div>
            </div>
            @error('project_type')
                <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <!-- Message Field -->
        <div class="transition-all duration-700 delay-300 transform" x-bind:class="appear ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <label for="message" class="block text-sm font-bold mb-2">Message</label>
            <textarea
                id="message"
                wire:model.live="message"
                rows="4"
                class="w-full px-4 md:px-5 py-3.5 md:py-4 rounded-xl md:rounded-2xl bg-zinc-50 dark:bg-zinc-900 border-2 {{ $errors->has('message') ? 'border-red-400 dark:border-red-800' : 'border-zinc-100 dark:border-zinc-800' }} focus:border-mint focus:outline-none transition-colors resize-none text-base"
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
        <div class="transition-all duration-700 delay-500 transform" x-bind:class="appear ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'">
            <button
                type="submit"
            wire:loading.attr="disabled"
            wire:loading.class="opacity-70 cursor-not-allowed"
            class="w-full bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 font-black py-3.5 md:py-4 rounded-xl md:rounded-2xl hover:scale-[1.02] transition-all flex items-center justify-center gap-3 text-sm md:text-base"
        >
            <span wire:loading.remove>Send Message</span>
            <span wire:loading>Sending...</span>
            <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m22 2-7 20-4-9-9-4z"/><path d="M22 2 11 13"/></svg>
            <svg wire:loading class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
        </button>
        </div>
    </form>
</div>
