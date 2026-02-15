<main class="pt-32 pb-20 px-6 lg:px-12 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-20">
        <!-- Left Column: Contact Info -->
        <div>
            <h1 class="text-6xl font-extrabold tracking-tighter mb-10 leading-[1.1]">
                Let's start a <br><span class="text-mint">Conversation.</span>
            </h1>
            <p class="text-xl text-zinc-500 dark:text-zinc-400 mb-12 max-w-md">
                I'm always open to new opportunities, collaborations, or just a friendly chat about design systems.
            </p>

            <div class="space-y-8">
                <a href="mailto:hello@artaputra.design" class="flex items-center gap-6 group">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center group-hover:bg-mint transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-zinc-400">Email Me</p>
                        <p class="text-lg font-bold">hello@artaputra.design</p>
                    </div>
                </a>

                <div class="flex items-center gap-6 group">
                    <div class="w-14 h-14 rounded-2xl bg-zinc-50 dark:bg-zinc-900 flex items-center justify-center group-hover:bg-violet transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-zinc-400">Chat with Me</p>
                        <p class="text-lg font-bold">+1 (555) 000-1111</p>
                    </div>
                </div>
            </div>

            <div class="mt-20">
                <p class="text-xs font-black uppercase text-zinc-400 mb-8 tracking-widest">Connect Elsewhere</p>
                <div class="flex gap-4">
                    <a href="#" class="w-12 h-12 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:border-mint transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4"/><path d="M9 18c-4.51 2-5-2-7-2"/></svg>
                    </a>
                    <a href="#" class="w-12 h-12 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:border-mint transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
                    </a>
                    <a href="#" class="w-12 h-12 rounded-xl border border-zinc-200 dark:border-zinc-800 flex items-center justify-center hover:border-mint transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect width="4" height="12" x="2" y="9"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Column: Livewire Contact Form -->
        <div class="bg-zinc-50 dark:bg-zinc-900 p-8 md:p-12 rounded-[3rem]">
            <livewire:contact-form />
        </div>
    </div>
</main>
