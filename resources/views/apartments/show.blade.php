@extends('layouts.site')

@php
    $metaTitle = $apartment->meta_title ?? $apartment->name.' — '. __('Lake Garda apartment rental');
    $metaDesc = \App\Support\PlainText::fromHtml((string) ($apartment->meta_description ?? $apartment->short_description ?? ''));
    $canonical = $apartment->canonical_url ?? route('apartments.show', $apartment);
    $cover = $apartment->coverImagePath();
    $ogImage = $cover ? asset('storage/'.$cover) : null;
    $heroBgUrl = $cover ? asset('storage/'.$cover) : null;
    $galleryImages = $apartment->images->isNotEmpty()
        ? $apartment->images
        : ($cover ? collect([(object) ['path' => $cover, 'alt_text' => $apartment->name]]) : collect());
    $galleryInitialIndex = 0;
    if ($cover && $galleryImages->isNotEmpty()) {
        $found = $galleryImages->search(fn ($img) => ($img->path ?? '') === $cover);
        if ($found !== false) {
            $galleryInitialIndex = (int) $found;
        }
    }
    $galleryForJs = $galleryImages->map(function ($img) use ($apartment) {
        return [
            'url' => asset('storage/'.$img->path),
            'alt' => $img->alt_text ?? $apartment->name,
        ];
    })->values()->all();
    $addressLine = $apartment->address
        ? trim($apartment->address)
        : ($apartment->location_text
            ? \Illuminate\Support\Str::of(\App\Support\PlainText::fromHtml($apartment->location_text))->explode("\n")->map(fn ($l) => trim($l))->filter()->first()
            : null);
    if (! $addressLine) {
        $addressLine = __('Lake Garda, Italy');
    }
    $mapSearchUrl = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($addressLine);
    // Google Maps embed for this address (no API key; pin shows search result).
    // z≈11–12 shows town + Lake Garda context; 15 was too tight (mostly streets).
    $mapEmbedFromAddressUrl = 'https://maps.google.com/maps?q='.rawurlencode($addressLine).'&z=11&output=embed&hl='.rawurlencode(str_replace('_', '-', app()->getLocale()));
    $galleryCount = $galleryImages->count();
@endphp

@push('meta')
<x-seo-meta
    :title="$metaTitle"
    :description="$metaDesc"
    :canonical="$canonical"
    :og-title="$apartment->og_title ?? $metaTitle"
    :og-description="$apartment->og_description ?? $metaDesc"
    :og-image="$ogImage"
/>
@endpush

@push('schema')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'VacationRental',
    'name' => $apartment->name,
    'description' => \App\Support\PlainText::fromHtml($apartment->short_description),
    'url' => route('apartments.show', $apartment),
    'image' => $apartment->images->map(fn ($i) => asset('storage/'.$i->path))->values()->all(),
    'occupancy' => [
        '@type' => 'QuantitativeValue',
        'maxValue' => $apartment->max_guests,
    ],
    'numberOfRooms' => $apartment->bedrooms,
    'address' => array_filter([
        '@type' => 'PostalAddress',
        'streetAddress' => $apartment->address ? trim($apartment->address) : null,
        'addressLocality' => 'Garda',
        'addressRegion' => 'VR',
        'addressCountry' => 'IT',
    ]),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => url('/')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => __('Apartments'), 'item' => url('/apartments')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $apartment->name, 'item' => route('apartments.show', $apartment)],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<div class="site-header-clear bg-white pb-10">
    {{-- Title bar (Booking-style) --}}
    <div class="border-b border-stone-200">
        <div class="mx-auto max-w-7xl px-4 pt-4 lg:px-8">
            <nav class="text-sm text-stone-500" aria-label="{{ __('Breadcrumb') }}">
                <a href="{{ route('home') }}" class="hover:text-lake-800">{{ __('Home') }}</a>
                <span class="mx-1.5">/</span>
                <a href="{{ route('apartments.index') }}" class="hover:text-lake-800">{{ __('Apartments') }}</a>
                <span class="mx-1.5">/</span>
                <span class="text-stone-800">{{ $apartment->name }}</span>
            </nav>
            <div class="flex flex-col gap-4 py-6 lg:flex-row lg:items-start lg:justify-between lg:gap-8">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2 text-amber-500" aria-hidden="true">
                        @for($s = 0; $s < 5; $s++)
                            <svg class="h-5 w-5 fill-current" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        @endfor
                    </div>
                    <p class="sr-only">{{ __('Featured property') }}</p>
                    <h1 class="mt-2 font-display text-3xl font-bold tracking-tight text-stone-900 md:text-4xl">{{ $apartment->name }}</h1>
                    <div class="mt-3 flex flex-wrap gap-2 sm:gap-2.5" role="list" aria-label="{{ __('Property details') }}">
                        <div class="inline-flex items-center gap-2 rounded-md border border-stone-200 bg-white py-1.5 pl-2 pr-3 text-[0.8125rem] font-semibold leading-snug text-stone-800 shadow-sm sm:text-sm" role="listitem">
                            <svg class="h-5 w-5 flex-shrink-0 text-stone-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            <span>{{ $apartment->max_guests }} {{ __('guests') }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-md border border-stone-200 bg-white py-1.5 pl-2 pr-3 text-[0.8125rem] font-semibold leading-snug text-stone-800 shadow-sm sm:text-sm" role="listitem">
                            <svg class="h-5 w-5 flex-shrink-0 text-stone-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M7.875 14.25A1.125 1.125 0 019 15.375v4.5m0-4.5a1.125 1.125 0 011.125-1.125h4.125a1.125 1.125 0 011.125 1.125v4.5M7.875 14.25H5.25m2.625 0v-4.5a1.125 1.125 0 011.125-1.125h4.125a1.125 1.125 0 011.125 1.125v4.5m-9 0h9.375"/></svg>
                            <span>{{ $apartment->bedrooms }} {{ __('bedrooms') }}</span>
                        </div>
                        <div class="inline-flex items-center gap-2 rounded-md border border-stone-200 bg-white py-1.5 pl-2 pr-3 text-[0.8125rem] font-semibold leading-snug text-stone-800 shadow-sm sm:text-sm" role="listitem">
                            <svg class="h-5 w-5 flex-shrink-0 text-stone-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            <span>{{ $apartment->bathrooms }} {{ __('bathrooms') }}</span>
                        </div>
                        @if($apartment->size_m2)
                        <div class="inline-flex items-center gap-2 rounded-md border border-stone-200 bg-white py-1.5 pl-2 pr-3 text-[0.8125rem] font-semibold leading-snug text-stone-800 shadow-sm sm:text-sm" role="listitem">
                            <svg class="h-5 w-5 flex-shrink-0 text-stone-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/></svg>
                            <span>{{ $apartment->size_m2 }} m²</span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="flex w-full flex-shrink-0 flex-col items-end gap-4 sm:w-auto" x-data="{ copyMsg: @js(__('Link copied to clipboard')) }">
                    <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
                        <button type="button" class="inline-flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-full border-2 border-lake-800 bg-white text-lake-900 shadow-sm transition hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-lake-500 focus-visible:ring-offset-2" @click="if (navigator.share) { navigator.share({ title: document.title, url: location.href }).catch(() => {}); } else if (navigator.clipboard) { navigator.clipboard.writeText(location.href).then(() => alert(copyMsg)); }" aria-label="{{ __('Share') }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                        </button>
                        <a href="#inquiry" class="inline-flex min-h-[2.75rem] items-center justify-center rounded-full border-2 border-lake-800 bg-white px-6 py-2.5 text-base font-semibold text-lake-900 shadow-sm transition hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-lake-500 focus-visible:ring-offset-2">{{ __('Reserve your stay') }}</a>
                    </div>
                    <p class="flex items-center gap-2 text-sm text-stone-600">
                        <svg class="h-5 w-5 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ __('Best price when you book direct') }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if($galleryImages->isNotEmpty())
    <section id="gallery" class="mx-auto max-w-7xl px-4 pt-6 lg:px-8" aria-label="{{ __('Photo gallery') }}"
        x-data="{
            items: @js($galleryForJs),
            active: {{ $galleryInitialIndex }},
            lightboxOpen: false,
            openLightbox(i) {
                this.active = i;
                this.lightboxOpen = true;
                document.body.classList.add('overflow-hidden');
                this.$nextTick(() => this.$refs.lightboxPanel?.focus({ preventScroll: true }));
            },
            closeLightbox() {
                this.lightboxOpen = false;
                document.body.classList.remove('overflow-hidden');
            },
            lbNext() {
                this.active = (this.active + 1) % this.items.length;
            },
            lbPrev() {
                this.active = (this.active - 1 + this.items.length) % this.items.length;
            },
        }"
        @keydown.escape.window="lightboxOpen && closeLightbox()"
        @keydown.arrow-right.window.prevent="lightboxOpen && lbNext()"
        @keydown.arrow-left.window.prevent="lightboxOpen && lbPrev()"
    >
        <div class="lg:grid lg:grid-cols-12 lg:items-stretch lg:gap-4">
            <div class="min-w-0 space-y-4 lg:col-span-8 xl:col-span-9">
        {{-- Top: main (2/3) + 2 stacked on right (1/3) — click opens full-screen gallery --}}
        <div class="flex flex-col gap-2 lg:h-[min(380px,42vw)] lg:min-h-[280px] lg:max-h-[460px] lg:flex-row lg:gap-2">
            <button type="button" class="group relative aspect-[16/9] w-full cursor-zoom-in overflow-hidden rounded-xl bg-stone-100 text-left sm:aspect-[16/10] lg:aspect-auto lg:h-full lg:min-h-0 lg:flex-[2] lg:rounded-l-2xl lg:rounded-r-none" @click="openLightbox(active)" aria-label="{{ __('Open photo gallery') }}">
                <img
                    :src="items[active].url"
                    :alt="items[active].alt"
                    class="h-full w-full object-cover transition group-hover:brightness-[0.97]"
                    width="1200"
                    height="675"
                    loading="eager"
                    decoding="async"
                    fetchpriority="high"
                />
                <span class="pointer-events-none absolute inset-0 ring-1 ring-inset ring-black/5 lg:rounded-l-2xl"></span>
            </button>
            <div class="hidden min-h-0 flex-col gap-2 overflow-hidden rounded-r-2xl ring-1 ring-black/5 lg:flex lg:h-full lg:flex-1 lg:min-w-0">
                <button type="button" class="relative min-h-0 flex-1 cursor-zoom-in overflow-hidden bg-stone-100 text-left" x-show="items.length >= 2" @click="openLightbox((active + 1) % items.length)" aria-label="{{ __('Open gallery photo') }}">
                    <img :src="items[(active + 1) % items.length].url" :alt="items[(active + 1) % items.length].alt" class="h-full w-full object-cover transition hover:opacity-95" width="400" height="240" loading="lazy"/>
                    <span class="absolute inset-0 ring-1 ring-inset ring-black/5"></span>
                </button>
                <button type="button" class="relative min-h-0 flex-1 cursor-zoom-in overflow-hidden bg-stone-100 text-left" x-show="items.length >= 3" @click="openLightbox((active + 2) % items.length)" aria-label="{{ __('Open gallery photo') }}">
                    <img :src="items[(active + 2) % items.length].url" :alt="items[(active + 2) % items.length].alt" class="h-full w-full object-cover transition hover:opacity-95" width="400" height="240" loading="lazy"/>
                    <span class="absolute inset-0 ring-1 ring-inset ring-black/5"></span>
                </button>
            </div>
        </div>
        @if($galleryCount > 1)
        {{-- Single row: up to 4 thumbs + optional 5th with +N (Booking-style) --}}
        <div class="mt-4 flex w-full flex-nowrap gap-2 overflow-x-auto pb-1 [-ms-overflow-style:none] [scrollbar-width:thin] [&::-webkit-scrollbar]:h-1.5 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-thumb]:bg-stone-300" role="tablist" aria-label="{{ __('Gallery thumbnails') }}">
            @for($i = 0; $i < min(4, $galleryCount); $i++)
                @php $img = $galleryImages->get($i); @endphp
                <button
                    type="button"
                    class="group relative aspect-[4/3] min-h-0 min-w-[4.5rem] flex-[1_1_0] overflow-hidden rounded-lg sm:min-w-[5.5rem]"
                    :class="active === {{ $i }} ? 'ring-2 ring-blue-600 ring-offset-2' : 'ring-1 ring-stone-200'"
                    @click="openLightbox({{ $i }})"
                    role="tab"
                    :aria-selected="(active === {{ $i }}).toString()"
                    aria-label="{{ __('Photo :num of :total', ['num' => $i + 1, 'total' => $galleryCount]) }}"
                >
                    <img src="{{ asset('storage/'.$img->path) }}" alt="" class="h-full w-full object-cover group-hover:opacity-95" @if($i < 6) loading="eager" @else loading="lazy" @endif decoding="async"/>
                </button>
            @endfor
            @if($galleryCount > 5)
                <button
                    type="button"
                    class="relative aspect-[4/3] min-h-0 min-w-[4.5rem] flex-[1_1_0] overflow-hidden rounded-lg ring-1 ring-stone-200 sm:min-w-[5.5rem]"
                    @click="openLightbox(4)"
                    aria-label="{{ __('Open gallery — more photos') }}"
                >
                    <img src="{{ asset('storage/'.$galleryImages->get(4)->path) }}" alt="" class="h-full w-full object-cover" loading="lazy" decoding="async"/>
                    <span class="absolute inset-0 bg-stone-900/50"></span>
                    <span class="absolute inset-0 flex items-center justify-center text-lg font-bold text-white drop-shadow">+{{ $galleryCount - 5 }}</span>
                </button>
            @elseif($galleryCount === 5)
                <button
                    type="button"
                    class="group relative aspect-[4/3] min-h-0 min-w-[4.5rem] flex-[1_1_0] overflow-hidden rounded-lg"
                    :class="active === 4 ? 'ring-2 ring-blue-600 ring-offset-2' : 'ring-1 ring-stone-200'"
                    @click="openLightbox(4)"
                    role="tab"
                    aria-label="{{ __('Photo :num of :total', ['num' => 5, 'total' => 5]) }}"
                >
                    <img src="{{ asset('storage/'.$galleryImages->get(4)->path) }}" alt="" class="h-full w-full object-cover group-hover:opacity-95" loading="lazy" decoding="async"/>
                </button>
            @endif
        </div>
        @endif
            </div>

            {{-- Map: apartment location (desktop right column — matches full gallery column height incl. thumbnails) --}}
            <aside class="mt-6 flex min-h-0 flex-col lg:col-span-4 xl:col-span-3 lg:mt-0 lg:h-full lg:self-stretch" aria-label="{{ __('Location map') }}">
                <div class="relative min-h-[260px] flex-1 overflow-hidden rounded-2xl border border-stone-200 bg-stone-200 shadow-sm ring-1 ring-stone-200/60 lg:h-full lg:min-h-0">
                    <iframe src="{{ $mapEmbedFromAddressUrl }}" class="absolute inset-0 h-full w-full border-0" title="{{ __('Map of :address', ['address' => $addressLine]) }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </div>
            </aside>
        </div>

        {{-- Full-screen lightbox --}}
        <div
            x-show="lightboxOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed inset-0 z-[200]"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('Photo gallery') }}"
        >
            <div class="absolute inset-0 bg-black/95" @click="closeLightbox()" aria-hidden="true"></div>
            <div class="relative z-10 flex h-full min-h-0 flex-col">
                <div class="flex shrink-0 items-center justify-between gap-4 border-b border-white/10 px-4 py-3 text-white">
                    <p class="text-sm font-medium tabular-nums">
                        <span x-text="active + 1"></span> / <span x-text="items.length"></span>
                    </p>
                    <button type="button" class="rounded-lg p-2 text-white hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-white" @click="closeLightbox()" aria-label="{{ __('Close gallery') }}">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div
                    class="relative flex min-h-0 flex-1 cursor-zoom-out items-center justify-center px-4 py-4 sm:px-12"
                    x-ref="lightboxPanel"
                    tabindex="-1"
                    @click="closeLightbox()"
                >
                    <button type="button" class="absolute left-2 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white backdrop-blur hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:left-4" @click.stop="lbPrev()" aria-label="{{ __('Previous photo') }}">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <img
                        :src="items[active].url"
                        :alt="items[active].alt"
                        class="relative z-10 max-h-[calc(100vh-10rem)] max-w-full cursor-default object-contain select-none"
                        width="1920"
                        height="1080"
                        loading="eager"
                        decoding="async"
                        draggable="false"
                        @click.stop
                    />
                    <button type="button" class="absolute right-2 top-1/2 z-20 -translate-y-1/2 rounded-full bg-white/10 p-3 text-white backdrop-blur hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:right-4" @click.stop="lbNext()" aria-label="{{ __('Next photo') }}">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                <p class="shrink-0 border-t border-white/10 px-4 py-3 text-center text-sm text-stone-300" x-text="items[active] ? items[active].alt : ''"></p>
            </div>
        </div>
    </section>
    @endif

    {{-- Amenities: Booking-style chips (line icons + label) --}}
    @if($apartment->amenities->isNotEmpty())
    <section class="mx-auto max-w-7xl px-4 py-10 lg:px-8">
        <div class="flex flex-wrap gap-2.5 sm:gap-3">
            @foreach($apartment->amenities as $am)
                <div class="inline-flex max-w-full items-center gap-2.5 rounded-lg border border-stone-200 bg-white py-2.5 pl-3 pr-4 text-[15px] font-medium leading-snug text-stone-800 shadow-sm sm:text-base sm:leading-snug">
                    <x-amenity-icon :amenity="$am" class="text-stone-600" />
                    <span>{{ $am->name }}</span>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Two columns: about + sticky sidebar --}}
    <div class="mx-auto max-w-7xl min-w-0 px-4 lg:px-8">
        {{-- minmax(0,1fr) via arbitrary grid so text wraps in-column (avoids min-content overflow under sidebar) --}}
        <div class="grid min-w-0 gap-10 lg:grid-cols-[repeat(12,minmax(0,1fr))] lg:gap-12 lg:items-stretch">
            <div class="min-w-0 max-w-full overflow-x-clip lg:col-span-8">
                <section class="min-w-0 max-w-full">
                    <h2 class="font-display text-2xl font-semibold text-stone-900">{{ __('About this property') }}</h2>
                    @if($apartment->ideal_for)
                        <p class="mt-2 text-sm font-medium text-olive-800">{{ __('Ideal for:') }} {{ $apartment->ideal_for }}</p>
                    @endif
                    {{-- Quill HTML: wrap long lines / URLs; prose styles headings, strong, lists --}}
                    <div class="prose prose-stone prose-base mt-6 w-full min-w-0 max-w-full [overflow-wrap:anywhere] prose-headings:font-semibold prose-headings:leading-snug prose-strong:font-semibold prose-strong:text-stone-900 prose-a:break-words prose-a:text-lake-800 prose-a:font-medium prose-p:my-2 prose-p:max-w-full prose-p:text-[15px] prose-p:leading-snug prose-li:max-w-full prose-li:text-[15px] prose-li:leading-snug [&_iframe]:max-w-full [&_img]:h-auto [&_img]:max-w-full [&_pre]:max-w-full [&_pre]:overflow-x-auto [&_pre]:break-all [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto">{!! $apartment->full_description !!}</div>
                </section>

                <section class="mt-12 border-t border-stone-200 pt-10">
                    <h2 class="font-display text-2xl font-semibold text-stone-900">{{ __('Pricing') }}</h2>
                    <p class="mt-2 text-stone-600">{{ __('From') }} <strong class="text-stone-900">€{{ number_format($apartment->price_from, 0, ',', '.') }}</strong> {{ __('/ night (indicative)') }}</p>
                    @if($apartment->seasons->isNotEmpty())
                        <div class="mt-6 overflow-x-auto rounded-xl border border-stone-200">
                            <table class="min-w-full text-left text-sm">
                                <thead class="bg-stone-50 text-stone-700">
                                    <tr>
                                        <th class="px-4 py-3 font-semibold">{{ __('Season') }}</th>
                                        <th class="px-4 py-3 font-semibold">{{ __('Dates') }}</th>
                                        <th class="px-4 py-3 font-semibold">{{ __('From / night') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($apartment->seasons as $s)
                                        <tr class="border-t border-stone-100">
                                            <td class="px-4 py-3">{{ $s->label }}</td>
                                            <td class="px-4 py-3">{{ $s->start_date->format('d M') }} – {{ $s->end_date->format('d M') }}</td>
                                            <td class="px-4 py-3 font-medium">€{{ number_format($s->price_per_night, 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    @if($apartment->availability_note)
                        <div class="prose prose-stone prose-sm mt-6 w-full min-w-0 max-w-full [overflow-wrap:anywhere] rounded-xl bg-amber-50 p-4 text-amber-950 prose-p:my-1 prose-p:max-w-full [&_a]:break-words [&_a]:text-amber-900">{!! $apartment->availability_note !!}</div>
                    @endif
                </section>

                @if($apartment->address || $apartment->location_text)
                <section id="location" class="mt-12 border-t border-stone-200 pt-10">
                    <h2 class="font-display text-2xl font-semibold text-stone-900">{{ __('Location') }}</h2>
                    @if($apartment->address)
                        <p class="mt-4 text-base font-medium text-stone-900">{{ $apartment->address }}</p>
                    @endif
                    @if($apartment->location_text)
                        <div class="prose prose-stone prose-sm mt-4 w-full min-w-0 max-w-full [overflow-wrap:anywhere] text-stone-700 prose-p:max-w-full [&_a]:break-words">{!! $apartment->location_text !!}</div>
                    @endif
                    <a href="{{ route('lake-garda') }}" class="mt-4 inline-block text-sm font-semibold text-blue-600 hover:underline">{{ __('Explore Lake Garda') }} →</a>
                </section>
                @endif

                @if($apartment->check_in_out_note)
                <section class="mt-10 border-t border-stone-200 pt-10">
                    <h2 class="font-display text-xl font-semibold text-stone-900">{{ __('Check-in & check-out') }}</h2>
                    <div class="prose prose-stone prose-sm mt-2 w-full min-w-0 max-w-full [overflow-wrap:anywhere] text-stone-600 prose-p:max-w-full [&_a]:break-words">{!! $apartment->check_in_out_note !!}</div>
                </section>
                @endif

                @if($apartment->license_cir || $apartment->license_cin)
                <section class="mt-10 border-t border-stone-200 pt-10" aria-labelledby="rental-id-heading">
                    <h2 id="rental-id-heading" class="font-display text-xl font-semibold text-stone-900">{{ __('Tourist rental identification') }}</h2>
                    <p class="mt-2 text-sm text-stone-600">{{ __('Mandatory codes for short-term rentals in Italy (L.R. Veneto / national register).') }}</p>
                    <dl class="mt-5 space-y-2 text-sm">
                        @if($apartment->license_cir)
                        <div class="flex flex-wrap gap-x-2 gap-y-1">
                            <dt class="text-stone-500">{{ __('CIR') }}</dt>
                            <dd class="font-mono text-[0.9375rem] tabular-nums text-stone-900">{{ $apartment->license_cir }}</dd>
                        </div>
                        @endif
                        @if($apartment->license_cin)
                        <div class="flex flex-wrap gap-x-2 gap-y-1">
                            <dt class="text-stone-500">{{ __('CIN') }}</dt>
                            <dd class="font-mono text-[0.9375rem] tabular-nums text-stone-900">{{ $apartment->license_cin }}</dd>
                        </div>
                        @endif
                    </dl>
                </section>
                @endif
            </div>

            <aside class="min-w-0 lg:col-span-4">
                <div class="space-y-6 lg:sticky lg:top-28">
                    <div class="rounded-xl border border-stone-200 bg-white p-5 shadow-sm ring-1 ring-stone-100">
                        <p class="text-sm text-stone-600">{{ __('From') }}</p>
                        <p class="mt-1 text-3xl font-bold text-stone-900">€{{ number_format($apartment->price_from, 0, ',', '.') }} <span class="text-lg font-normal text-stone-600">{{ __('/ night') }}</span></p>
                        <a href="#inquiry" class="mt-4 flex w-full items-center justify-center rounded-full border-2 border-lake-800 bg-white py-3 text-center text-base font-semibold text-lake-900 shadow-sm transition hover:bg-stone-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-lake-500 focus-visible:ring-offset-2">{{ __('Reserve your stay') }}</a>
                        <p class="mt-3 text-center text-xs text-stone-500">{{ __('We will confirm your dates personally.') }}</p>
                    </div>

                    <div class="rounded-xl border border-stone-200 bg-stone-50 p-5">
                        <h3 class="text-sm font-semibold text-stone-900">{{ __('Property highlights') }}</h3>
                        <ul class="mt-3 space-y-2 text-sm text-stone-700">
                            <li class="flex gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg> {{ $addressLine }}</li>
                            @if($apartment->size_m2)
                                <li class="flex gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg> {{ $apartment->size_m2 }} m²</li>
                            @endif
                            @foreach($apartment->amenities as $am)
                                <li class="flex gap-2"><svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg> {{ $am->name }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>

<section id="inquiry" class="border-t border-stone-200 bg-stone-100 py-16">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <h2 class="font-display text-2xl font-semibold text-lake-950">{{ __('Request this apartment') }}</h2>
        <p class="mt-2 max-w-2xl text-sm text-stone-600">{{ __('No instant booking—send your dates and we will confirm availability personally.') }}</p>
        <div class="mt-8 rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8 lg:p-10">
            <x-inquiry-form
                :action-url="route('inquiry.store')"
                :apartments="$apartments"
                :selected-apartment-id="$apartment->id"
                :source-page="'apartment-'.$apartment->slug"
                :submit-label="__('Send booking request')"
            />
        </div>
    </div>
</section>
@endsection
