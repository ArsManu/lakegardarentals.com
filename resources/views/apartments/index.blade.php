@extends('layouts.site')

@php
    use App\Support\MediaUrl;
    $b = $page?->blocks ?? [];
    $headerHeroSrc = MediaUrl::public($b['hero_header_image_path'] ?? '');
    $metaTitle = trans_page_string($page?->meta_title, 'Lake Garda apartments in Garda');
    $metaDesc = trans_page_string(
        (filled($page?->meta_description) ? $page->meta_description : null),
        'Compare our two holiday apartments in Garda on Lake Garda. Direct booking inquiries, clear pricing, and fast host responses.'
    );
    $canonical = $page?->canonical_url ?? localized_route('apartments.index');
    $ogTitle = filled($page?->og_title) ? trans_page_string($page->og_title, '') : null;
    $ogDesc = filled($page?->og_description) ? trans_page_string($page->og_description, '') : null;
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

@section('content')
<x-inner-page-hero
    :image-src="$headerHeroSrc"
    :title="$b['hero_title'] ?? null"
    title-fallback="Our apartments"
    :subtitle="$b['hero_subtitle'] ?? null"
    subtitle-fallback="Two thoughtfully prepared rentals in Garda—ideal for couples and families exploring Lake Garda."
    :page-label="__('Apartments')"
/>

@if(! empty($b['flex_blocks']))
    <x-page-flex-blocks :blocks="$b['flex_blocks']" />
@endif

<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="grid gap-10 md:grid-cols-2">
            @foreach($apartments as $apt)
                <x-apartment-card :apartment="$apt" />
            @endforeach
        </div>
    </div>
</section>
@endsection
