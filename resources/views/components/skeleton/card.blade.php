<div {{ $attributes->merge(['class' => 'animate-pulse bg-zinc-50 dark:bg-zinc-900 rounded-[2rem] overflow-hidden']) }}>
    @isset($image)
        <div class="aspect-[4/3] bg-zinc-200 dark:bg-zinc-800"></div>
    @endisset

    <div class="p-8 space-y-4">
        @isset($title)
            <div class="flex items-center gap-2 mb-3">
                <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded-full w-16"></div>
                <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded-full w-2"></div>
                <div class="h-3 bg-zinc-200 dark:bg-zinc-800 rounded-full w-12"></div>
            </div>
            <div class="h-6 bg-zinc-200 dark:bg-zinc-800 rounded w-3/4"></div>
        @endisset

        @isset($description)
            <div class="space-y-2">
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded w-full"></div>
                <div class="h-4 bg-zinc-200 dark:bg-zinc-800 rounded w-5/6"></div>
            </div>
        @endisset

        @isset($meta)
            <div class="flex gap-2 pt-2">
                <div class="h-6 bg-zinc-200 dark:bg-zinc-800 rounded-full w-16"></div>
                <div class="h-6 bg-zinc-200 dark:bg-zinc-800 rounded-full w-20"></div>
            </div>
        @endisset

        {{ $slot }}
    </div>
</div>
