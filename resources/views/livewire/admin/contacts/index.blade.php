<div>
    <!-- Page Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold">Messages</h1>
                @if($unreadCount > 0)
                    <span class="px-3 py-1 bg-mint/10 text-mint text-sm font-bold rounded-full">{{ $unreadCount }} new</span>
                @endif
            </div>
            <p class="text-zinc-500 mt-1 text-sm sm:text-base">Contact form submissions</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-4">
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
                    <input type="text" wire:model.live="search" placeholder="Search messages..."
                           class="w-full pl-11 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                </div>
            </div>

            <!-- Status Filter -->
            <div class="sm:w-40">
                <select wire:model.live="statusFilter"
                        class="w-full px-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 focus:border-mint focus:outline-none transition-colors text-sm">
                    <option value="">All Messages</option>
                    <option value="unread">Unread</option>
                    <option value="read">Read</option>
                </select>
            </div>
        </div>
    </div>

    @if(count($contacts) > 0)
        {{-- Bulk Actions Bar --}}
        @if(count($selected) > 0)
            <div class="bg-mint/10 dark:bg-mint/20 border border-mint/30 rounded-xl p-4 mb-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ count($selected) }} selected</span>
                    <div class="flex items-center gap-2">
                        <select wire:model="bulkAction" class="px-3 py-1.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm focus:ring-2 focus:ring-mint">
                            <option value="">Select Action</option>
                            <option value="markAsRead">Mark as Read</option>
                            <option value="markAsUnread">Mark as Unread</option>
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

        <!-- Skeleton Loading -->
        <div wire:loading.delay class="space-y-3">
            @for($i = 0; $i < 3; $i++)
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 p-4 sm:p-6">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-4 h-4 mt-1 rounded bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-zinc-200 dark:bg-zinc-700 animate-pulse flex-shrink-0"></div>
                        <div class="flex-1 space-y-2 min-w-0">
                            <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-32 animate-pulse"></div>
                            <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-48 animate-pulse"></div>
                        </div>
                    </div>
                    <div class="space-y-2 ml-14">
                        <div class="h-4 bg-zinc-200 dark:bg-zinc-700 rounded w-3/4 animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-full animate-pulse"></div>
                        <div class="h-3 bg-zinc-200 dark:bg-zinc-700 rounded w-2/3 animate-pulse"></div>
                    </div>
                </div>
            @endfor
        </div>

        <div wire:loading.remove class="space-y-3 sm:space-y-4">
            @foreach($contacts as $contact)
                <div wire:key="{{ $contact->id }}"
                     class="bg-white dark:bg-zinc-900 rounded-2xl border {{ $contact->is_read ? 'border-zinc-200 dark:border-zinc-800' : 'border-mint/30 dark:border-mint/30' }} overflow-hidden">
                    <!-- Header -->
                    <div class="p-4 sm:p-6 flex items-start justify-between gap-3 sm:gap-4">
                        <div class="flex items-start gap-3 sm:gap-4 flex-1 min-w-0">
                            <!-- Checkbox -->
                            <input type="checkbox" value="{{ $contact->id }}" wire:model.live="selected" class="mt-1 sm:mt-2 rounded border-zinc-300 dark:border-zinc-600 text-mint focus:ring-mint flex-shrink-0">
                            <!-- Avatar -->
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-base sm:text-lg font-bold text-zinc-500 flex-shrink-0">
                                {{ strtoupper(substr($contact->name, 0, 1)) }}
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <h3 class="font-bold text-sm sm:text-base truncate">{{ $contact->name }}</h3>
                                    @if(!$contact->is_read)
                                        <span class="w-2 h-2 bg-mint rounded-full flex-shrink-0"></span>
                                    @endif
                                </div>
                                <div class="text-xs sm:text-sm text-zinc-500 truncate">{{ $contact->email }}</div>
                                <div class="text-xs text-zinc-400 mt-1 sm:hidden">
                                    {{ $contact->created_at->format('M d, Y') }} · {{ $contact->created_at->format('h:i A') }}
                                </div>
                            </div>
                        </div>

                        <!-- Date & Actions -->
                        <div class="hidden sm:flex items-center gap-4 flex-shrink-0">
                            <div class="text-right">
                                <div class="text-sm text-zinc-500">{{ $contact->created_at->format('M d, Y') }}</div>
                                <div class="text-xs text-zinc-400">{{ $contact->created_at->format('h:i A') }}</div>
                            </div>

                            <!-- Actions Dropdown -->
                            <div class="relative" x-data="{ open: false }" x-cloak>
                                <button x-on:click="open = !open"
                                        class="p-2 hover:bg-zinc-100 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="1"/>
                                        <circle cx="19" cy="12" r="1"/>
                                        <circle cx="5" cy="12" r="1"/>
                                    </svg>
                                </button>

                                <div x-show="open" x-on:click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-lg z-10"
                                     style="display: none;">
                                    @if($contact->is_read)
                                        <button wire:click="markAsUnread({{ $contact->id }})"
                                                class="w-full px-4 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 first:rounded-t-xl transition-colors">
                                            Mark as unread
                                        </button>
                                    @else
                                        <button wire:click="markAsRead({{ $contact->id }})"
                                                class="w-full px-4 py-2 text-left text-sm hover:bg-zinc-50 dark:hover:bg-zinc-800 first:rounded-t-xl transition-colors">
                                            Mark as read
                                        </button>
                                    @endif
                                    <button wire:click="delete({{ $contact->id }})"
                                            wire:confirm="Are you sure you want to delete this message?"
                                            class="w-full px-4 py-2 text-left text-sm text-red-500 hover:bg-zinc-50 dark:hover:bg-zinc-800 last:rounded-b-xl transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subject & Message -->
                    <div class="px-4 sm:px-6 pb-4 sm:pb-6">
                        <div class="mb-3">
                            <span class="text-xs text-zinc-500 uppercase tracking-wider">Subject</span>
                            <h4 class="font-bold text-sm sm:text-base mt-1">{{ $contact->subject }}</h4>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500 uppercase tracking-wider">Message</span>
                            <p class="text-zinc-600 dark:text-zinc-400 mt-1 text-sm whitespace-pre-wrap">{{ $contact->message }}</p>
                        </div>
                    </div>

                    <!-- Mobile Actions -->
                    <div class="flex sm:hidden items-center gap-2 px-4 pb-4">
                        @if($contact->is_read)
                            <button wire:click="markAsUnread({{ $contact->id }})" class="flex-1 py-2 text-xs font-bold bg-zinc-100 dark:bg-zinc-800 rounded-lg hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                                Mark Unread
                            </button>
                        @else
                            <button wire:click="markAsRead({{ $contact->id }})" class="flex-1 py-2 text-xs font-bold bg-mint/10 text-mint rounded-lg hover:bg-mint/20 transition-colors">
                                Mark Read
                            </button>
                        @endif
                        <button wire:click="delete({{ $contact->id }})" wire:confirm="Are you sure you want to delete this message?" class="flex-1 py-2 text-xs font-bold bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                            Delete
                        </button>
                    </div>

                    <!-- Footer -->
                    @if($contact->is_read)
                        <div class="px-4 sm:px-6 py-3 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200 dark:border-zinc-800">
                            <p class="text-xs text-zinc-500">
                                Read on {{ $contact->read_at->format('M d, Y') }} at {{ $contact->read_at->format('h:i A') }}
                            </p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-6 px-2 sm:px-0">
            {{ $contacts->links() }}
        </div>
    @else
        <div class="text-center py-16 sm:py-20 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 mx-2 sm:mx-0">
            <div class="w-14 h-14 sm:w-16 sm:h-16 mx-auto mb-4 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     class="text-zinc-400">
                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
            </div>
            <p class="text-zinc-500 text-sm sm:text-base">No messages found.</p>
        </div>
    @endif
</div>
