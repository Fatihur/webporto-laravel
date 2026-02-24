@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'url' => null,
    'type' => 'website',
    'author' => 'Fatihurroyyan',
    'noindex' => false,
    'locale' => null,
    'publishedTime' => null,
    'modifiedTime' => null,
])

@php
$siteName = config('app.name', 'Fatih Porto');
$defaultDescription = config('app.meta_description', 'Fatihurroyyan (Fatih) - Tech enthusiast & developer specializing in software development, graphic design, data analysis, and networking.');
$defaultImage = asset('images/og-default.jpg');
$twitterSite = config('app.twitter_site');

$metaTitle = $title ? "{$title} | {$siteName}" : $siteName;
$metaDescription = strip_tags($description ?? $defaultDescription);
$metaKeywords = $keywords ?? 'fatihurroyyan, fatih, portfolio fatihurroyyan, portfolio fatih, software development, graphic design, data analysis, networking, tech enthusiast';
$metaImage = $image ?? $defaultImage;
$metaUrl = $url ?? url()->current();
$canonicalUrl = preg_replace('/\?.*/', '', (string) $metaUrl);
$metaLocale = $locale ?? str_replace('-', '_', config('app.locale', 'en'));
$robotsContent = $noindex
    ? 'noindex, nofollow'
    : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
@endphp

{{-- Primary Meta Tags --}}
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="author" content="{{ $author }}">
<meta name="robots" content="{{ $robotsContent }}">
<meta name="googlebot" content="{{ $robotsContent }}">
<meta name="referrer" content="strict-origin-when-cross-origin">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $metaTitle }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:locale" content="{{ $metaLocale }}">

@if ($type === 'article' && $publishedTime)
    <meta property="article:published_time" content="{{ $publishedTime }}">
@endif

@if ($type === 'article' && $modifiedTime)
    <meta property="article:modified_time" content="{{ $modifiedTime }}">
@endif

{{-- Twitter --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ $metaUrl }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
<meta name="twitter:image:alt" content="{{ $metaTitle }}">

@if ($twitterSite)
    <meta name="twitter:site" content="{{ $twitterSite }}">
@endif

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="alternate" hreflang="{{ str_replace('_', '-', $metaLocale) }}" href="{{ $canonicalUrl }}">
