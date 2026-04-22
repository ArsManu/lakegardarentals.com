@props([
    'title',
    'description' => null,
    'canonical' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'ogImage' => null,
    'noindex' => false,
    'hreflangUrls' => null,
])

<title>{{ $title }} — {{ $siteName }}</title>
@if($noindex)
<meta name="robots" content="noindex, nofollow">
@else
<meta name="robots" content="index, follow">
@endif
@if($description)
<meta name="description" content="{{ $description }}">
@endif
@if($canonical)
<link rel="canonical" href="{{ $canonical }}">
@endif
@if (! $noindex)
@foreach(($hreflangUrls ?? localized_hreflang_urls()) as $lang => $hUrl)
    @if($hUrl)
        <link rel="alternate" hreflang="{{ $lang === 'x-default' ? 'x-default' : $lang }}" href="{{ $hUrl }}">
    @endif
@endforeach
@endif
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $ogTitle ?? $title }}">
@if($ogDescription ?? $description)
<meta property="og:description" content="{{ $ogDescription ?? $description }}">
@endif
@if($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $ogTitle ?? $title }}">
@if($ogDescription ?? $description)
<meta name="twitter:description" content="{{ $ogDescription ?? $description }}">
@endif
