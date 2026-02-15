@props([
    'lines' => 3,
    'width' => 'full',
])

<div {{ $attributes->merge(['class' => 'animate-pulse space-y-2']) }}>
    @for ($i = 0; $i < $lines; $i++)
        @php
            $lineWidth = $width;
            if ($width === 'random') {
                $widths = ['w-full', 'w-5/6', 'w-4/5', 'w-3/4', 'w-2/3'];
                $lineWidth = $widths[array_rand($widths)];
            }
            if ($i === $lines - 1 && $lines > 1) {
                $lineWidth = 'w-2/3';
            }
        @endphp
        <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded {{ $lineWidth }}"></div>
    @endfor
</div>
