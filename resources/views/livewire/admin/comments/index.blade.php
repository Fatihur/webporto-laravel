<x-slot name="header">
    <h1 class="text-2xl sm:text-3xl font-bold">Comments Management</h1>
</x-slot>

<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
                <input
                    type="text"
                    wire:model="search"
                    placeholder="Search comments..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 focus:border-mint focus:outline-none transition-colors text-sm"
                >
            </div>
        </div>
    </div>

    <!-- Skeleton Loading - Desktop -->
    <div wire:loading.delay class="hidden md:block bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-4 py-4 text-left">
                        <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                    </th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Blog</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Author</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Content</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Date</th>
                    <th class="px-6 py-4 text-right text-xs font-bold uppercase text-zinc-500">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @for($i = 0; $i < 5; $i++)
                    <tr>
                        <td class="px-4 py-4">
                            <div class="w-4 h-4 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="space-y-2">
                                <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-24 animate-pulse"></div>
                                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-full animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-8 ml-auto animate-pulse"></div>
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Skeleton Loading - Mobile -->
    <div wire:loading.delay class="md:hidden space-y-3">
        @for($i = 0; $i < 3; $i++)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-4 h-4 mt-1 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                    <div class="flex-1 space-y-2 min-w-0">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-1/2 animate-pulse"></div>
                    </div>
                </div>
                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-full mb-3 animate-pulse"></div>
                <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-2/3 mb-3 animate-pulse"></div>
                <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-700">
                    <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-20 animate-pulse"></div>
                    <div class="w-8 h-8 bg-zinc-200 dark:bg-zinc-700 rounded-lg animate-pulse"></div>
                </div>
            </div>
        @endfor
    </div>

    {{-- Bulk Actions Bar --}}
    @if(count($selected) > 0)
        <div class="bg-mint/10 dark:bg-mint/20 border border-mint/30 rounded-xl p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ count($selected) }} selected</span>
                <div class="flex items-center gap-2">
                    <select wire:model="bulkAction" class="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm focus:ring-2 focus:ring-mint">
                        <option value="">Select Action</option>
                        <option value="approve">Approve</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button wire:click="executeBulkAction" wire:confirm="Are you sure you want to execute this action?"
                            class="px-4 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">
                        Execute
                    </button>
                </div>
            </div>
            <button wire:click="$set('selected', [])" class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                Clear selection
            </button>
        </div>
    @endif

    <!-- Desktop Table -->
    <div wire:loading.remove class="hidden md:block bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Blog</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Author</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Content</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Date</th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase text-zinc-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($comments as $comment)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition-colors">
                            <td class="px-4 py-4">
                                <input type="checkbox" value="{{ $comment->id }}" wire:model.live="selected" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate text-sm font-medium">
                                    {{ $comment->blog?->title ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-sm">{{ $comment->name }}</div>
                                <div class="text-xs text-zinc-500">{{ $comment->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs text-sm text-zinc-500 line-clamp-2">
                                    {{ $comment->content }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-zinc-500 text-sm">
                                {{ $comment->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="delete({{ $comment->id }})" wire:confirm="Are you sure you want to delete this comment?" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                </div>
                                <p>No comments found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div wire:loading.remove class="md:hidden space-y-3">
        @forelse ($comments as $comment)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-4" wire:key="mobile-{{ $comment->id }}">
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" value="{{ $comment->id }}" wire:model.live="selected" class="mt-1 rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="font-bold text-sm">{{ $comment->name }}</div>
                        <div class="text-xs text-zinc-500">{{ $comment->email }}</div>
                    </div>
                </div>

                <div class="text-sm text-zinc-600 dark:text-zinc-300 mb-3 ml-7 line-clamp-3">
                    {{ $comment->content }}
                </div>

                <div class="text-xs text-zinc-500 mb-3 ml-7 line-clamp-1">
                    On: {{ $comment->blog?->title ?? 'N/A' }}
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-700 ml-7">
                    <span class="text-xs text-zinc-500">
                        {{ $comment->created_at->format('M d, Y') }}
                    </span>
                    <button wire:click="delete({{ $comment->id }})" wire:confirm="Are you sure you want to delete this comment?" class="p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-red-500 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 p-8 text-center text-zinc-500">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-400">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <p>No comments found.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($comments->hasPages())
        <div class="mt-6 px-2 sm:px-0">
            {{ $comments->links() }}
        </div>
    @endif
</div>
