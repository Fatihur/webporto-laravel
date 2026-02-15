<div>
    <!-- Page Title -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold">Dashboard</h1>
        <p class="text-zinc-500 mt-1">Welcome back! Here's what's happening with your portfolio.</p>
    </div>

    <!-- Skeleton Loading -->
    <div wire:loading.delay class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @for($i = 0; $i < 4; $i++)
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-center justify-between">
                    <div class="space-y-2">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        <div class="h-8 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                </div>
            </div>
        @endfor
    </div>

    <!-- Stats Grid -->
    <div wire:loading.remove class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Projects -->
        <a href="{{ route('admin.projects.index') }}" wire:navigate class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-mint transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-1">Projects</p>
                    <p class="text-3xl font-black">{{ $projectsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-mint/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-mint">
                        <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                        <polyline points="9 22 9 12 15 12 15 22"/>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Blog Posts -->
        <a href="{{ route('admin.blogs.index') }}" wire:navigate class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-violet transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-1">Blog Posts</p>
                    <p class="text-3xl font-black">{{ $blogsCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-violet">
                        <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/>
                        <path d="M18 14h-8"/>
                        <path d="M15 18h-5"/>
                        <path d="M10 6h8v4h-8V6Z"/>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Contacts -->
        <a href="{{ route('admin.contacts.index') }}" wire:navigate class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-lime transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-1">Messages</p>
                    <p class="text-3xl font-black">{{ $contactsCount }}</p>
                    @if($unreadContactsCount > 0)
                        <p class="text-xs text-mint mt-1">{{ $unreadContactsCount }} unread</p>
                    @endif
                </div>
                <div class="w-12 h-12 rounded-xl bg-lime/10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-lime">
                        <path d="M22 17a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9.5C2 7 4 5 6.5 5H18c2.2 0 4 1.8 4 4v8Z"/>
                        <polyline points="15 9 12 12 9 9"/>
                    </svg>
                </div>
            </div>
        </a>

        <!-- Experience -->
        <a href="{{ route('admin.experiences.index') }}" wire:navigate class="bg-white dark:bg-zinc-900 p-6 rounded-2xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-400 transition-colors">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500 mb-1">Experience</p>
                    <p class="text-3xl font-black">{{ $experiencesCount }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-500">
                        <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                        <line x1="16" x2="16" y1="2" y2="6"/>
                        <line x1="8" x2="8" y1="2" y2="6"/>
                        <line x1="3" x2="21" y1="10" y2="10"/>
                    </svg>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-6">
        <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('admin.projects.create') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                New Project
            </a>

            <a href="{{ route('admin.blogs.create') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                New Blog Post
            </a>

            <a href="{{ route('admin.experiences.create') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-3 border border-zinc-200 dark:border-zinc-800 rounded-xl font-bold hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"/>
                    <path d="M12 5v14"/>
                </svg>
                Add Experience
            </a>
        </div>
    </div>
</div>
