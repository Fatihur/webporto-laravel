<div class="p-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold">Newsletter Subscribers</h1>
            <p class="text-zinc-500 mt-1">Manage your newsletter subscribers and send updates.</p>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="exportCsv" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors text-sm font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" x2="12" y1="15" y2="3"/>
                </svg>
                Export CSV
            </button>

            <a href="{{ route('admin.newsletter.send') }}" wire:navigate class="px-4 py-2 bg-mint text-zinc-950 rounded-lg hover:scale-105 transition-transform text-sm font-bold">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-2">
                    <path d="m22 2-7 20-4-9-9-4Z"/>
                    <path d="M22 2 11 13"/>
                </svg>
                Send Newsletter
            </a>
        </div>
    </div>

    {{-- Settings & Queue Status --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Auto Newsletter Setting --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-mint/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-mint">
                        <path d="m22 2-7 20-4-9-9-4Z"/>
                        <path d="M22 2 11 13"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg">Auto Newsletter</h3>
                    <p class="text-zinc-500 text-sm mt-1">Automatically send newsletter when new blog or project is published.</p>

                    <div class="mt-4 flex items-center gap-3">
                        <button
                            wire:click="toggleAutoNewsletter"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $autoNewsletterEnabled ? 'bg-mint' : 'bg-zinc-200 dark:bg-zinc-700' }}"
                        >
                            <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $autoNewsletterEnabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                        </button>
                        <span class="text-sm font-medium {{ $autoNewsletterEnabled ? 'text-green-600' : 'text-zinc-500' }}">
                            {{ $autoNewsletterEnabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Queue Status --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600 dark:text-blue-400">
                        <path d="M12 8v4l3 3"/>
                        <circle cx="12" cy="12" r="10"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg">Queue Status</h3>
                    <p class="text-zinc-500 text-sm mt-1">Pending and failed email jobs.</p>

                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($pendingJobs > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">
                            {{ $pendingJobs }} pending
                        </span>
                        @endif

                        @if($failedJobs > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                            {{ $failedJobs }} failed
                        </span>
                        @endif

                        @if($pendingJobs === 0 && $failedJobs === 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                            All caught up!
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Total Subscribers</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="w-12 h-12 bg-zinc-100 dark:bg-zinc-700 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-zinc-600 dark:text-zinc-400">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Active</p>
                    <p class="text-3xl font-bold mt-1 text-green-600">{{ number_format($stats['active']) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl p-6 border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-zinc-500">Unsubscribed</p>
                    <p class="text-3xl font-bold mt-1 text-red-600">{{ number_format($stats['unsubscribed']) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="17" x2="22" y1="8" y2="13"/>
                        <line x1="22" x2="17" y1="8" y2="13"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions Bar --}}
    @if(count($selected) > 0)
        <div class="bg-mint/10 dark:bg-mint/20 border border-mint/30 rounded-xl p-4 mb-4 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ count($selected) }} selected</span>
                <select wire:model="bulkAction" class="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm focus:ring-2 focus:ring-mint">
                    <option value="">Select Action</option>
                    <option value="activate">Activate</option>
                    <option value="deactivate">Deactivate</option>
                    <option value="delete">Delete</option>
                </select>
                <button wire:click="executeBulkAction" wire:confirm="Are you sure you want to execute this action?"
                        class="px-4 py-1.5 bg-zinc-950 dark:bg-white text-white dark:text-zinc-950 rounded-lg text-sm font-bold hover:opacity-90 transition-opacity">
                    Execute
                </button>
            </div>
            <button wire:click="$set('selected', [])" class="text-sm text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300">
                Clear selection
            </button>
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 mb-6">
        <div class="p-4 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by email or name..."
                        class="w-full pl-10 pr-4 py-2 bg-zinc-100 dark:bg-zinc-900 border-0 rounded-lg text-sm focus:ring-2 focus:ring-mint"
                    >
                </div>
            </div>

            <select wire:model.live="statusFilter" class="px-4 py-2 bg-zinc-100 dark:bg-zinc-900 border-0 rounded-lg text-sm focus:ring-2 focus:ring-mint">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="unsubscribed">Unsubscribed</option>
            </select>
        </div>
    </div>

    {{-- Subscribers Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-4 py-4 text-left">
                            <input type="checkbox" wire:model.live="selectAll" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                        </th>
                        <th class="px-6 py-4 text-left">
                            <button wire:click="sortBy('email')" class="flex items-center gap-2 text-xs font-bold uppercase text-zinc-500 hover:text-zinc-700">
                                Email
                                @if($sortField === 'email')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $sortDirection === 'asc' ? 'rotate-180' : '' }} transition-transform">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase text-zinc-500">Name</th>
                        <th class="px-6 py-4 text-left">
                            <button wire:click="sortBy('status')" class="flex items-center gap-2 text-xs font-bold uppercase text-zinc-500 hover:text-zinc-700">
                                Status
                                @if($sortField === 'status')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $sortDirection === 'asc' ? 'rotate-180' : '' }} transition-transform">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-left">
                            <button wire:click="sortBy('subscribed_at')" class="flex items-center gap-2 text-xs font-bold uppercase text-zinc-500 hover:text-zinc-700">
                                Subscribed
                                @if($sortField === 'subscribed_at')
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $sortDirection === 'asc' ? 'rotate-180' : '' }} transition-transform">
                                        <path d="m6 9 6 6 6-6"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold uppercase text-zinc-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse($subscribers as $subscriber)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30">
                            <td class="px-4 py-4">
                                <input type="checkbox" value="{{ $subscriber->id }}" wire:model.live="selected" class="rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium">{{ $subscriber->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-zinc-500">{{ $subscriber->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($subscriber->isActive())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                        Unsubscribed
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-zinc-500">{{ $subscriber->subscribed_at?->diffForHumans() ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button
                                        wire:click="toggleStatus({{ $subscriber->id }})"
                                        class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                        title="{{ $subscriber->isActive() ? 'Unsubscribe' : 'Activate' }}"
                                    >
                                        @if($subscriber->isActive())
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <line x1="17" x2="22" y1="8" y2="13"/>
                                                <line x1="22" x2="17" y1="8" y2="13"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <line x1="19" x2="19" y1="8" y2="14"/>
                                                <line x1="22" x2="16" y1="11" y2="11"/>
                                            </svg>
                                        @endif
                                    </button>

                                    <button
                                        wire:click="deleteSubscriber({{ $subscriber->id }})"
                                        wire:confirm="Are you sure you want to delete this subscriber?"
                                        class="p-2 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors"
                                        title="Delete"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-500">
                                            <path d="M3 6h18"/>
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-zinc-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="mx-auto mb-4">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No subscribers found</p>
                                    <p class="text-sm mt-1">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($subscribers->hasPages())
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $subscribers->links() }}
            </div>
        @endif
    </div>
</div>
