@extends('layouts.site')

@php
    use App\Support\MediaUrl;
    $b = $page->blocks ?? [];
    $headerHeroSrc = MediaUrl::public($b['hero_header_image_path'] ?? '');
    $metaTitle = $page->meta_title ?? __('Lake Garda & Garda — holiday guide');
    $metaDesc = $page->meta_description;
    $canonical = $page->canonical_url ?? url('/lake-garda');
@endphp

@push('meta')
<x-seo-meta :title="$metaTitle" :description="$metaDesc" :canonical="$canonical" :og-title="$page->og_title ?? $metaTitle" :og-description="$page->og_description ?? $metaDesc" />
@endpush

@section('content')
<x-inner-page-hero
    :image-src="$headerHeroSrc"
    :title="$b['hero_title'] ?? __('Lake Garda from Garda')"
    :subtitle="$b['hero_subtitle'] ?? ''"
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
