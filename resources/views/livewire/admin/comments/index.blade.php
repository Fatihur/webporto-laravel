<x-slot name="header">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Comments Management</h1>
</x-slot>

<div class="space-y-6">
    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
        <div class="flex flex-col sm:flex-row gap-4">
            <input
                type="text"
                wire:model="search"
                placeholder="Search comments..."
                class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
        </div>
    </div>

    <!-- Skeleton Loading -->
    <div wire:loading.delay class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="p-6 space-y-4">
            @for($i = 0; $i < 5; $i++)
                <div class="flex gap-4">
                    <div class="w-32 h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                    <div class="flex-1 h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                    <div class="w-24 h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                    <div class="w-20 h-4 bg-gray-200 dark:bg-gray-700 rounded animate-pulse"></div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Mobile Skeleton -->
    <div wire:loading.delay class="md:hidden space-y-4">
        @for($i = 0; $i < 3; $i++)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 space-y-3">
                <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 animate-pulse"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-full animate-pulse"></div>
                <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-2/3 animate-pulse"></div>
            </div>
        @endfor
    </div>

    {{-- Bulk Actions Bar --}}
    @if(count($selected) > 0)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ count($selected) }} selected</span>
                <select wire:model="bulkAction" class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Action</option>
                    <option value="approve">Approve</option>
                    <option value="delete">Delete</option>
                </select>
                <button wire:click="executeBulkAction" wire:confirm="Are you sure you want to execute this action?"
                        class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    Execute
                </button>
            </div>
            <button wire:click="$set('selected', [])" class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                Clear selection
            </button>
        </div>
    @endif

    <!-- Desktop Table -->
    <div wire:loading.remove class="hidden md:block bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Blog
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Author
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Content
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($comments as $comment)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <input type="checkbox" value="{{ $comment->id }}" wire:model.live="selected" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="max-w-xs truncate text-sm text-gray-900 dark:text-white">
                                    {{ $comment->blog?->title ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->email }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ $comment->content }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $comment->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="delete({{ $comment->id }})" wire:confirm="Are you sure you want to delete this comment?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                No comments found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Cards -->
    <div wire:loading.remove class="md:hidden space-y-4">
        @forelse ($comments as $comment)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <div class="font-medium text-gray-900 dark:text-white">{{ $comment->name }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $comment->email }}</div>
                    </div>
                </div>

                <div class="text-sm text-gray-600 dark:text-gray-300 mb-3 line-clamp-3">
                    {{ $comment->content }}
                </div>

                <div class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    On: {{ $comment->blog?->title ?? 'N/A' }}
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $comment->created_at->format('M d, Y') }}
                    </span>
                    <button wire:click="delete({{ $comment->id }})" wire:confirm="Are you sure you want to delete this comment?" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                    </button>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-8 text-center text-gray-500 dark:text-gray-400">
                No comments found.
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($comments->hasPages())
        <div class="flex justify-center mt-6">
            {{ $comments->links() }}
        </div>
    @endif
</div>
