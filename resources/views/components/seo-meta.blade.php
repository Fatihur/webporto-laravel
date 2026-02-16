@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'url' => null,
    'type' => 'website',
    'author' => 'Fatih',
])

@php
$siteName = config('app.name', 'Fatih Porto');
$defaultDescription = config('app.meta_description', 'Portfolio of Fatih - Tech enthusiast specializing in design, development, and complex system architectures.');
$defaultImage = asset('images/og-default.jpg');

$metaTitle = $title ? "{$title} | {$siteName}" : $siteName;
$metaDescription = $description ?? $defaultDescription;
$metaKeywords = $keywords ?? 'portfolio, design, development, web, graphic design, software development';
$metaImage = $image ?? $defaultImage;
$metaUrl = $url ?? url()->current();
@endphp

{{-- Primary Meta Tags --}}
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<meta name="keywords" content="{{ $metaKeywords }}">
<meta name="author" content="{{ $author }}">

{{-- Open Graph / Facebook --}}
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $metaUrl }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:site_name" content="{{ $siteName }}">

{{-- Twitter --}}
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $metaUrl }}">
<meta property="twitter:title" content="{{ $metaTitle }}">
<meta property="twitter:description" content="{{ $metaDescription }}">
<meta property="twitter:image" content="{{ $metaImage }}">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $metaUrl }}">
