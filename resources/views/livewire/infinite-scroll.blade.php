<div>
    <!-- Items Container -->
    <div class="space-y-4">
        @foreach ($items as $item)
            <div wire:key="{{ $item->id }}">
                @yield('item', $item)
            </div>
        @endforeach
    </div>

    <!-- Loading State -->
    @if ($isLoading)
        <div class="py-8">
            <x-loading-spinner size="lg" />
        </div>
    @endif

    <!-- Load More Trigger -->
    <div 
        x-intersect="if (!$isLoading && hasMorePages) { $wire.loadMore() }"
        class="py-4"
    >
        @if ($hasMorePages && !$isLoading)
            <div class="text-center text-gray-500 dark:text-gray-400 text-sm">
                Scroll down to load more...
            </div>
        @elseif (!$hasMorePages && count($items) > 0)
            <div class="text-center text-gray-500 dark:text-gray-400 text-sm">
                You have reached the end
            </div>
        @endif
    </div>

    <!-- Empty State -->
    @if (empty($items) && !$isLoading)
        <div class="py-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="mt-2 text-gray-600 dark:text-gray-400">No items found</p>
        </div>
    @endif
</div>
