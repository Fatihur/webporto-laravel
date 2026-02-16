<div>
    <!-- Search Input -->
    <div class="mb-6">
        <div class="relative">
            <input
                type="text"
                wire:model="query"
                wire:keydown.enter="search"
                placeholder="Search projects, blogs..."
                class="w-full px-4 py-3 pl-12 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
            >
            <svg class="absolute left-3 top-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-2a2 2 0 012-2l-6 6m2-2a2 2 0 01-2m-6 6m2-2a2 2 0 012-2z" />
            </svg>
        </div>
    </div>

    <!-- Type Filter -->
    <div class="flex gap-2 mb-6">
        <button
            wire:click="$set('type', 'all')"
            class="{{ $type === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-4 py-2 rounded-lg font-medium transition duration-200"
        >
All
        </button>
        <button
            wire:click="$set('type', 'projects')"
            class="{{ $type === 'projects' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-4 py-2 rounded-lg font-medium transition duration-200"
        >
Projects
        </button>
        <button
            wire:click="$set('type', 'blogs')"
            class="{{ $type === 'blogs' ? 'bg-blue-600 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }} px-4 py-2 rounded-lg font-medium transition duration-200"
        >
Blogs
        </button>
    </div>

    <!-- Results -->
    @if (strlen($query) >= 3)
        <div class="space-y-4">
            @forelse ($results as $result)
                @if ($result['type'] === 'project')
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-md transition duration-200">
                        <a href="{{ route('projects.show', $result['data']->slug) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $result['data']->title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-3">
                                {{ Str::limit($result['data']->description, 150) }}
                            </p>
                            @if ($result['data']->tech_stack)
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($result['data']->tech_stack as $tech)
                                        <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full">
                                            {{ $tech }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </a>
                    </div>
                @elseif ($result['type'] === 'blog')
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-4 shadow hover:shadow-md transition duration-200">
                        <a href="{{ route('blog.show', $result['data']->slug) }}" class="block">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                {{ $result['data']->title }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-3">
                                {{ Str::limit($result['data']->excerpt, 150) }}
                            </p>
                            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $result['data']->author }}</span>
                                <span>•</span>
                                <span>{{ $result['data']->published_at?->format('M d, Y') }}</span>
                            </div>
                        </a>
                    </div>
                @endif
            @empty
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>No results found.</p>
                </div>
            @endforelse
        </div>
    @else
        <div class="text-center py-12 text-gray-500 dark:text-gray-400">
            <p>Enter at least 3 characters to search...</p>
        </div>
    @endif
</div>
