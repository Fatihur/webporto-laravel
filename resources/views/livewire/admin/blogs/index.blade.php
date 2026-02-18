<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold">Blog Posts</h1>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Manage your blog articles</p>
        </div>
        <a href="{{ route('admin.blogs.create') }}" wire:navigate
           class="inline-flex items-center justify-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/>
                <path d="M12 5v14"/>
            </svg>
            <span class="hidden sm:inline">New Post</span>
            <span class="sm:hidden">New</span>
        </a>
    </div>

    <!-- Filters & Bulk Actions -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="text-zinc-400">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>
                    <input type="text" wire:model.live="search" placeholder="Search posts..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-3">
                <select wire:model.live="categoryFilter"
                        class="px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}">{{ ucwords($cat) }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter"
                        class="px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Status</option>
                    <option value="published">Published</option>
                    <option value="drafts">Drafts</option>
                </select>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        @if(count($selected) > 0)
            <div class="mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-800 flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="text-sm text-zinc-500">{{ count($selected) }} selected</span>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <select wire:model="bulkAction" class="flex-1 sm:flex-none px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                        <option value="">Select Action</option>
                        <option value="publish">Publish</option>
                        <option value="unpublish">Unpublish</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button wire:click="executeBulkAction" wire:confirm="Are you sure you want to execute this action on {{ count($selected) }} items?" class="px-4 py-2 bg-mint text-zinc-950 rounded-xl font-bold hover:bg-mint/80 transition-colors text-sm">
                        Execute
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Skeleton Loading - Desktop -->
    <div wire:loading.delay class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
            <tr>
                <th class="px-6 py-4 text-left">
                    <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                </th>
                <th class="px-6 py-4 text-left text-sm font-bold">Title</th>
                <th class="px-6 py-4 text-left text-sm font-bold">Category</th>
                <th class="px-6 py-4 text-left text-sm font-bold">Published</th>
                <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
            @for($i = 0; $i < 5; $i++)
                <tr>
                    <td class="px-6 py-4">
                        <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                            <div class="space-y-2">
                                <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16 mx-auto animate-pulse"></div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-16 ml-auto animate-pulse"></div>
                    </td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>

    <!-- Skeleton Loading - Mobile -->
    <div wire:loading.delay class="md:hidden space-y-3">
        @for($i = 0; $i < 5; $i++)
            <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-4 h-4 mt-1 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                    <div class="w-12 h-12 rounded-lg bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                    <div class="flex-1 space-y-2 min-w-0">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="h-5 bg-zinc-200 dark:bg-zinc-700 rounded w-16 animate-pulse"></div>
                    <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-12 animate-pulse"></div>
                </div>
                <div class="flex gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex-1 h-9 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                    <div class="flex-1 h-9 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                </div>
            </div>
        @endfor
    </div>

    @if(count($blogs) > 0)
        <!-- Desktop Table -->
        <div wire:loading.remove class="hidden md:block bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-4 text-left">
                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-zinc-300 text-mint focus:ring-mint">
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors" wire:click="sortBy('title')">
                        Title
                        @if($sortField === 'title')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-left text-sm font-bold">Category</th>
                    <th class="px-6 py-4 text-left text-sm font-bold cursor-pointer hover:text-mint transition-colors" wire:click="sortBy('published_at')">
                        Published
                        @if($sortField === 'published_at')
                            <span class="ml-1">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-6 py-4 text-center text-sm font-bold">Status</th>
                    <th class="px-6 py-4 text-right text-sm font-bold">Actions</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @foreach($blogs as $blog)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $blog->id }}">
                        <td class="px-6 py-4">
                            <input type="checkbox" value="{{ $blog->id }}" wire:model.live="selected" class="w-4 h-4 rounded border-zinc-300 text-mint focus:ring-mint">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                @if($blog->image)
                                    <img src="{{ Storage::url($blog->image) }}" alt="" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                            <rect width="18" height="18" x="3" y="3" rx="2"/>
                                            <circle cx="9" cy="9" r="2"/>
                                            <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold">{{ Str::limit($blog->title, 40) }}</div>
                                    <div class="text-xs text-zinc-500">{{ $blog->read_time }} min read</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 bg-violet/10 text-violet text-xs font-bold rounded-full uppercase">{{ $blog->category }}</span>
                        </td>
                        <td class="px-6 py-4 text-zinc-500 text-sm">
                            @if($blog->published_at)
                                {{ $blog->published_at->format('M d, Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <button wire:click="togglePublish({{ $blog->id }})" class="px-3 py-1 text-xs font-bold rounded-full transition-colors {{ $blog->is_published ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                                {{ $blog->is_published ? 'Published' : 'Draft' }}
                            </button>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.blogs.edit', $blog->id) }}" wire:navigate class="text-sm font-bold hover:text-mint transition-colors">Edit</a>
                                <button wire:click="delete({{ $blog->id }})" wire:confirm="Are you sure you want to delete this blog post?" class="text-sm font-bold text-red-500 hover:text-red-600 transition-colors">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards -->
        <div wire:loading.remove class="md:hidden space-y-3">
            @foreach($blogs as $blog)
                <div class="bg-white dark:bg-zinc-900 p-4 rounded-xl border border-zinc-200 dark:border-zinc-800" wire:key="mobile-{{ $blog->id }}">
                    <!-- Header: Image + Title + Checkbox -->
                    <div class="flex items-start gap-3 mb-3">
                        <input type="checkbox" value="{{ $blog->id }}" wire:model.live="selected" class="mt-1 rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                        @if($blog->image)
                            <img src="{{ Storage::url($blog->image) }}" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                    <circle cx="9" cy="9" r="2"/>
                                    <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-sm line-clamp-2">{{ $blog->title }}</h3>
                            <p class="text-xs text-zinc-500">{{ $blog->read_time }} min read</p>
                        </div>
                    </div>

                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="px-2 py-0.5 bg-violet/10 text-violet text-xs font-bold rounded-full uppercase">{{ $blog->category }}</span>
                        @if($blog->published_at)
                            <span class="text-xs text-zinc-500">{{ $blog->published_at->format('M d, Y') }}</span>
                        @endif
                    </div>

                    <!-- Status & Actions -->
                    <div class="flex items-center gap-2 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <button wire:click="togglePublish({{ $blog->id }})" class="px-3 py-1.5 text-xs font-bold rounded-full transition-colors {{ $blog->is_published ? 'bg-green-100 text-green-600' : 'bg-zinc-100 text-zinc-500' }}">
                            {{ $blog->is_published ? 'Published' : 'Draft' }}
                        </button>
                        <a href="{{ route('admin.blogs.edit', $blog->id) }}" wire:navigate class="flex-1 py-2.5 text-xs font-bold bg-mint/10 text-mint rounded-lg text-center hover:bg-mint/20 transition-colors">Edit</a>
                        <button wire:click="delete({{ $blog->id }})" wire:confirm="Are you sure you want to delete this blog post?" class="flex-1 py-2.5 text-xs font-bold bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-2 sm:px-0">
            {{ $blogs->links() }}
        </div>
    </div>

    @else
        <div class="text-center py-16 sm:py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 mx-2 sm:mx-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                    <path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-4 0v-9a2 2 0 0 1 2-2h2"/>
                    <rect width="8" height="4" x="10" y="6" rx="1"/>
                    <path d="M10 14h4"/>
                    <path d="M10 18h4"/>
                </svg>
            </div>
            <p class="text-zinc-500 mb-4 text-sm sm:text-base">No blog posts found.</p>
            <a href="{{ route('admin.blogs.create') }}" wire:navigate class="inline-flex items-center gap-2 px-4 sm:px-6 py-3 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-xl font-bold hover:scale-105 transition-transform text-sm sm:text-base">
                Create First Post
            </a>
        </div>
    @endif
</div>
