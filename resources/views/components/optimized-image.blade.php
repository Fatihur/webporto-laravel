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
    @php $loading = 'eager'; @endphp
@endif

@php
    $placeholder = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxIiBoZWlnaHQ9IjEiLz4=';
@endphp

<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    class="{{ $class }}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    loading="{{ $loading }}"
    decoding="{{ $decoding }}"
    onerror="this.style.display='none'"
    @if(!$priority) style="content-visibility: auto;" @endif
/>
