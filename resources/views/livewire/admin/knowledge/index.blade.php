<div>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold">AI Knowledge Base</h1>
                <p class="text-zinc-500 dark:text-zinc-400 mt-1">Manage knowledge entries for AI assistant</p>
            </div>
            <a href="{{ route('admin.knowledge.create') }}" wire:navigate
               class="inline-flex items-center gap-2 px-6 py-3 bg-mint text-zinc-950 rounded-xl font-bold hover:bg-mint/80 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Add Entry
            </a>
        </div>
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Total Entries</p>
            <p class="text-3xl font-bold mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Active</p>
            <p class="text-3xl font-bold mt-1 text-green-600">{{ $stats['active'] }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Inactive</p>
            <p class="text-3xl font-bold mt-1 text-zinc-400">{{ $stats['inactive'] }}</p>
        </div>
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4">
            <p class="text-xs font-bold uppercase tracking-wider text-zinc-500">Most Used</p>
            <p class="text-3xl font-bold mt-1 text-mint">{{ $stats['mostUsed'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Search knowledge entries..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none">
            </div>

            <select wire:model.live="categoryFilter"
                    class="px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                @endforeach
            </select>

            <select wire:model.live="statusFilter"
                    class="px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(count($selected) > 0)
        <div class="bg-mint/10 border border-mint/20 rounded-xl p-4 mb-6 flex items-center gap-4">
            <span class="font-bold text-sm">{{ count($selected) }} selected</span>
            <select wire:model="bulkAction" class="px-3 py-1.5 rounded-lg text-sm border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900">
                <option value="">Bulk Actions</option>
                <option value="activate">Activate</option>
                <option value="deactivate">Deactivate</option>
                <option value="delete">Delete</option>
            </select>
            <button wire:click="executeBulkAction" class="px-4 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-sm font-bold">Apply</button>
        </div>
    @endif

    <!-- Table -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-950 border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-4 py-3 text-left">
                        <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-300">
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-bold cursor-pointer" wire:click="sortBy('title')">
                        Title
                        @if($sortField === 'title')
                            <span class="text-mint">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-bold">Category</th>
                    <th class="px-4 py-3 text-left text-sm font-bold cursor-pointer" wire:click="sortBy('usage_count')">
                        Usage
                        @if($sortField === 'usage_count')
                            <span class="text-mint">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-bold">Status</th>
                    <th class="px-4 py-3 text-left text-sm font-bold cursor-pointer" wire:click="sortBy('updated_at')">
                        Updated
                        @if($sortField === 'updated_at')
                            <span class="text-mint">{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span>
                        @endif
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-bold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                @forelse($entries as $entry)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-950/50">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selected" value="{{ $entry->id }}" class="rounded border-zinc-300">
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold">{{ $entry->title }}</p>
                            <p class="text-xs text-zinc-500 truncate max-w-xs">{{ Str::limit($entry->content, 60) }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg text-xs font-bold">
                                {{ $entry->category }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">{{ $entry->usage_count }}</td>
                        <td class="px-4 py-3">
                            <button wire:click="toggleStatus({{ $entry->id }})" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $entry->is_active ? 'bg-mint' : 'bg-zinc-300 dark:bg-zinc-700' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $entry->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-sm text-zinc-500">{{ $entry->updated_at->diffForHumans() }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.knowledge.edit', $entry->id) }}" wire:navigate
                                   class="p-2 text-zinc-500 hover:text-mint transition-colors" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                                    </svg>
                                </a>
                                <button wire:click="delete({{ $entry->id }})" wire:confirm="Are you sure you want to delete this entry?"
                                        class="p-2 text-zinc-500 hover:text-red-500 transition-colors" title="Delete">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 6h18M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="text-zinc-400">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-lg font-bold mb-2">No knowledge entries found</p>
                            <p class="text-zinc-500 mb-4">Start by adding your first knowledge entry</p>
                            <a href="{{ route('admin.knowledge.create') }}" wire:navigate
                               class="inline-flex items-center gap-2 px-4 py-2 bg-mint text-zinc-950 rounded-xl font-bold hover:bg-mint/80 transition-colors">
                                Add First Entry
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $entries->links() }}
    </div>
</div>
