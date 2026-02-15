<div {{ $attributes->merge(['class' => 'animate-pulse']) }}>
    <div class="bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
        @isset($image)
            <div class="aspect-video bg-gray-300 dark:bg-gray-600"></div>
        @endisset
        
        <div class="p-4 space-y-3">
            @isset($title)
                <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
            @endisset
            
            @isset($description)
                <div class="space-y-2">
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded"></div>
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-5/6"></div>
                </div>
            @endisset
            
            @isset($meta)
                <div class="flex gap-2 pt-2">
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                </div>
            @endisset
            
            {{ $slot }}
        </div>
    </div>
</div>
