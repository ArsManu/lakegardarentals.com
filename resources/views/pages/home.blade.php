@extends('layouts.site')

@php
    use App\Support\HtmlTranslationSanity;
    use App\Support\MediaUrl;
    $b = $page->blocks ?? [];
    $heroSlides = is_array($b['hero_slides'] ?? null) ? $b['hero_slides'] : [];
    $why = $b['why_points'] ?? [];
    $whySectionBgSrc = '';
    foreach ($heroSlides as $slide) {
        if (is_array($slide) && ($slide['image_path'] ?? '') !== '') {
            $whySectionBgSrc = MediaUrl::public($slide['image_path']);
            break;
        }
    }
    $ctaTitle = $b['cta_title'] ?? '';
    $ctaText = $b['cta_text'] ?? '';
    $metaTitle = filled($page->meta_title)
        ? trans_page_string($page->meta_title, '')
        : trans_page_string(null, 'Lake Garda apartments in Garda — holiday rentals');
    $metaDesc = trans_page_string(
        filled($page->meta_description) ? $page->meta_description : null,
        'Premium holiday apartments in Garda on Lake Garda. Direct booking inquiries, fast replies, and local hosting. Ideal for couples and families.'
    );
    $ogTitle = filled($page->og_title) ? trans_page_string($page->og_title, '') : null;
    $ogDesc = filled($page->og_description) ? trans_page_string($page->og_description, '') : null;
    $canonical = $page->canonical_url ?? localized_route('home');
    $showHero = false;
    foreach ($heroSlides as $slide) {
        if (is_array($slide) && ($slide['image_path'] ?? '') !== '') {
            $showHero = true;
            break;
        }
    }
@endphp

@push('meta')
<x-seo-meta
    :title="$metaTitle"
    :description="$metaDesc"
    :canonical="$canonical"
    :og-title="$ogTitle ?? $metaTitle"
    :og-description="$ogDesc ?? $metaDesc"
/>
@endpush

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $siteName,
    'url' => localized_route('home'),
    'telephone' => $sitePhone,
    'email' => $siteEmail,
    'address' => [
        '@type' => 'PostalAddress',
        'addressLocality' => 'Garda',
        'addressRegion' => 'VR',
        'addressCountry' => 'IT',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'WebSite',
    'name' => $siteName,
    'url' => localized_route('home'),
    'potentialAction' => [
        '@type' => 'SearchAction',
        'target' => localized_route('apartments.index'),
        'query-input' => 'required name=search_term_string',
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
@if($showHero)
    <x-home-hero-slider :slides="$heroSlides" />
@endif

@if(! empty($b['flex_blocks']))
    <div class="{{ $showHero ? '' : 'site-header-clear' }}">
        <x-page-flex-blocks :blocks="$b['flex_blocks']" />
    </div>
@endif

{{-- Apartments --}}
<section class="min-w-0 max-w-full bg-white py-16">
    <div class="mx-auto min-w-0 max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-4xl font-semibold tracking-tight text-lake-950 md:text-5xl">{{ __('Our apartments') }}</h2>
        <p class="mt-3 max-w-2xl text-lg leading-relaxed text-stone-600">{{ __('Two curated holiday rentals—each with its own character, minutes from the lake.') }}</p>
        <div class="mt-12 grid min-w-0 gap-10 md:grid-cols-2">
            @foreach($apartments as $apt)
                <x-apartment-card :apartment="$apt" />
            @endforeach
        </div>
    </div>
</section>

{{-- Why us --}}
@if(count($why))
<section class="relative isolate overflow-hidden border-y border-white/10 py-20 md:py-24">
    @if($whySectionBgSrc !== '')
        <div class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $whySectionBgSrc }}');" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-black/72" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-black/70 via-black/15 to-black/50" aria-hidden="true"></div>
    @else
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-lake-950 via-lake-900 to-stone-950" aria-hidden="true"></div>
        <div class="pointer-events-none absolute inset-0 bg-black/50" aria-hidden="true"></div>
    @endif
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="font-display text-[3rem] font-semibold leading-tight tracking-tight text-white drop-shadow-md">{{ __('Why book with us') }}</h2>
        <div class="mt-10 grid min-w-0 grid-cols-1 gap-6 sm:grid-cols-2 sm:gap-8 lg:grid-cols-4">
            @foreach($why as $item)
                <div class="min-w-0 rounded-2xl bg-white p-6 shadow-lg md:p-7">
                    <h3 class="font-display text-xl font-semibold tracking-tight text-lake-950 md:text-2xl">{{ $item['title'] ?? '' }}</h3>
                    <div class="prose prose-stone prose-sm mt-3 max-w-none break-words text-stone-600 prose-p:my-1 [&_p]:break-words">{!! HtmlTranslationSanity::toDisplayableHtml((string) ($item['text'] ?? '')) !!}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
