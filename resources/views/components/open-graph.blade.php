@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'url' => null,
    'type' => 'website',
    'siteName' => null,
    'locale' => null,
    'twitterCard' => 'summary_large_image',
    'twitterSite' => null,
    'twitterCreator' => null,
])

@php
    $title = $title ?? config('app.name');
    $description = $description ?? config('app.description', '');
    $image = $image ?? asset('images/og-default.jpg');
    $url = $url ?? request()->url();
    $siteName = $siteName ?? config('app.name');
    $locale = $locale ?? app()->getLocale();
    $twitterSite = $twitterSite ?? config('app.twitter_site');
@endphp

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $locale }}">

@if ($type === 'article')
    <meta property="article:published_time" content="{{ $publishedTime ?? null }}">
    <meta property="article:modified_time" content="{{ $modifiedTime ?? null }}">
    <meta property="article:author" content="{{ $author ?? null }}">
    @isset($tags)
        @foreach ($tags as $tag)
            <meta property="article:tag" content="{{ $tag }}">
        @endforeach
    @endif
@endif

<!-- Twitter -->
<meta name="twitter:card" content="{{ $twitterCard }}">
<meta name="twitter:url" content="{{ $url }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

@if ($twitterSite)
    <meta name="twitter:site" content="{{ $twitterSite }}">
@endif

@if ($twitterCreator)
    <meta name="twitter:creator" content="{{ $twitterCreator }}">
@endif

<!-- Additional image meta -->
@if (is_array($image))
    <meta property="og:image:width" content="{{ $image['width'] ?? 1200 }}">
    <meta property="og:image:height" content="{{ $image['height'] ?? 630 }}">
    <meta property="og:image:alt" content="{{ $image['alt'] ?? $title }}">
@endif
