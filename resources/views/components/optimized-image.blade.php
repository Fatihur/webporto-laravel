@props([
    'src',
    'alt' => '',
    'class' => '',
    'width' => null,
    'height' => null,
    'loading' => 'lazy',
    'decoding' => 'async',
    'priority' => false,
])

@if($priority)
    @php
        $loading = 'eager';
        $fetchpriority = 'high';
    @endphp
@else
    @php
        $fetchpriority = 'auto';
    @endphp
@endif

<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    class="{{ $class }}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    loading="{{ $loading }}"
    decoding="{{ $decoding }}"
    fetchpriority="{{ $fetchpriority }}"
    onerror="this.style.display='none'"
    @if(!$priority) style="content-visibility: auto;" @endif
/>
