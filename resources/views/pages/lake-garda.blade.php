@extends('layouts.site')

@php
    use App\Support\MediaUrl;
    $b = $page->blocks ?? [];
    $headerHeroSrc = MediaUrl::public($b['hero_header_image_path'] ?? '');
    $metaTitle = trans_page_string($page->meta_title, 'Lake Garda & Garda — beaches, old town, boat trips');
    $metaDesc = trans_page_string(
        filled($page->meta_description) ? $page->meta_description : null,
        'Why stay in Garda on Lake Garda: beaches, restaurants, hiking, family activities, and day trips. Plan your holiday apartment stay.'
    );
    $canonical = $page->canonical_url ?? localized_route('lake-garda');
    $ogTitle = filled($page->og_title) ? trans_page_string($page->og_title, '') : null;
    $ogDesc = filled($page->og_description) ? trans_page_string($page->og_description, '') : null;
@endphp

@push('meta')
<x-seo-meta :title="$metaTitle" :description="$metaDesc" :canonical="$canonical" :og-title="$ogTitle ?? $metaTitle" :og-description="$ogDesc ?? $metaDesc" />
@endpush

@section('content')
<x-inner-page-hero
    :image-src="$headerHeroSrc"
    :title="$b['hero_title'] ?? null"
    title-fallback="Lake Garda from Garda"
    :subtitle="$b['hero_subtitle'] ?? null"
    subtitle-fallback="Italy’s largest lake—olive groves, clear water, medieval towns, and a pace that feels like a real holiday."
    :page-label="__('Lake Garda')"
/>

@if(! empty($b['flex_blocks']))
    <x-page-flex-blocks :blocks="$b['flex_blocks']" />
@endif

@if(config('lakegarda.map_embed_url'))
<section class="py-14">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <h2 class="font-display text-3xl font-semibold text-lake-950">{{ __('Map') }}</h2>
        <div class="mt-6 aspect-video overflow-hidden rounded-2xl bg-stone-200 shadow">
            <iframe src="{{ config('lakegarda.map_embed_url') }}" class="h-full w-full border-0" title="{{ __('Map of Garda') }}" loading="lazy"></iframe>
        </div>
    </div>
</section>
@endif

@endsection
