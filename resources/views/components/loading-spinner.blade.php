@props([
    'size' => 'md',
    'color' => 'primary',
])

@php
    $sizes = [
        'sm' => 'h-4 w-4',
        'md' => 'h-8 w-8',
        'lg' => 'h-12 w-12',
        'xl' => 'h-16 w-16',
    ];
    
    $colors = [
        'primary' => 'border-blue-500',
        'white' => 'border-white',
        'gray' => 'border-gray-500',
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $colorClass = $colors[$color] ?? $colors['primary'];
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-center', 'role' => 'status']) }}>
    <div class="{{ $sizeClass }} border-2 {{ $colorClass }} border-t-transparent rounded-full animate-spin"></div>
    @isset($text)
        <span class="ml-2 text-gray-600 dark:text-gray-400">{{ $text }}</span>
    @endisset
    <span class="sr-only">Loading...</span>
</div>
