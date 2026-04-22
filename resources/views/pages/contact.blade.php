@extends('layouts.site')

@php
    use App\Support\HtmlTranslationSanity;
    use App\Support\MediaUrl;
    $b = $page->blocks ?? [];
    $headerHeroSrc = MediaUrl::public($b['hero_header_image_path'] ?? '');
    $metaTitle = trans_page_string($page->meta_title, 'Contact — booking requests Lake Garda');
    $metaDesc = trans_page_string(
        filled($page->meta_description) ? $page->meta_description : null,
        'Call, WhatsApp, or send a booking request for our apartments in Garda on Lake Garda. Fast replies.'
    );
    $canonical = $page->canonical_url ?? localized_route('contact');
    $ogTitle = filled($page->og_title) ? trans_page_string($page->og_title, '') : null;
    $ogDesc = filled($page->og_description) ? trans_page_string($page->og_description, '') : null;
    $reassuranceRaw = $b['reassurance'] ?? null;
    if ($reassuranceRaw === null || trim($reassuranceRaw) === '') {
        $reassuranceForDisplay = trans_page_string(null, 'We typically reply within a few hours during the day (CET). For urgent matters, call us.');
    } else {
        $reassuranceForDisplay = (strip_tags($reassuranceRaw) === $reassuranceRaw)
            ? trans_page_string($reassuranceRaw, '')
            : $reassuranceRaw;
    }
    $reassuranceHtml = HtmlTranslationSanity::toDisplayableHtml($reassuranceForDisplay);
    $waNumber = $siteWhatsapp ?: $sitePhoneTel;
    $waUrl = $waNumber ? 'https://wa.me/'.preg_replace('/\D/', '', $waNumber) : null;
@endphp

@push('meta')
<x-seo-meta :title="$metaTitle" :description="$metaDesc" :canonical="$canonical" :og-title="$ogTitle ?? $metaTitle" :og-description="$ogDesc ?? $metaDesc" />
@endpush

@section('content')
<x-inner-page-hero
    :image-src="$headerHeroSrc"
    :title="$b['hero_title'] ?? null"
    title-fallback="Contact & booking"
    :subtitle="$b['hero_subtitle'] ?? null"
    subtitle-fallback="Send your dates and questions—we reply as quickly as we can."
    :page-label="__('Contact')"
/>

@if(! empty($b['flex_blocks']))
    <x-page-flex-blocks :blocks="$b['flex_blocks']" wrapper-class="bg-white py-12" />
@endif

<section id="inquiry" class="mx-auto max-w-7xl px-4 py-14 lg:px-8">
    <div class="grid gap-12 lg:grid-cols-2">
        <div>
            <h2 class="font-display text-2xl font-semibold text-lake-950">{{ __('Get in touch') }}</h2>
            <div class="prose prose-stone prose-sm mt-2 max-w-none text-stone-600 prose-p:my-1">{!! $reassuranceHtml !!}</div>
            <ul class="mt-8 space-y-4">
                <li>
                    <a href="tel:{{ $sitePhoneTel }}" class="group inline-flex items-start gap-3 text-base font-medium text-lake-950 transition hover:text-lake-800">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-stone-200 bg-stone-50 text-gold-600 transition group-hover:border-gold-500/40 group-hover:bg-white group-hover:shadow-sm" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                        </span>
                        <span class="pt-1">{{ $sitePhone }}</span>
                    </a>
                </li>
                @if($waUrl)
                <li>
                    <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer" class="group inline-flex items-start gap-3 text-base font-medium text-lake-950 transition hover:text-lake-800">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-stone-200 bg-stone-50 text-green-600 transition group-hover:border-green-500/40 group-hover:bg-white group-hover:shadow-sm" aria-hidden="true">
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        </span>
                        <span class="pt-1">{{ __('Message us on WhatsApp') }}</span>
                    </a>
                </li>
                @endif
                <li>
                    <a href="mailto:{{ $siteEmail }}" class="group inline-flex items-start gap-3 text-base font-medium text-lake-950 transition hover:text-lake-800">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-stone-200 bg-stone-50 text-gold-600 transition group-hover:border-gold-500/40 group-hover:bg-white group-hover:shadow-sm" aria-hidden="true">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                        </span>
                        <span class="pt-1 lg:whitespace-nowrap">{{ $siteEmail }}</span>
                    </a>
                </li>
                <li class="inline-flex items-start gap-3 text-base leading-relaxed text-stone-600">
                    <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-stone-200 bg-stone-50 text-gold-600/90" aria-hidden="true">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" /></svg>
                    </span>
                    <span class="pt-1">{{ $siteAddress }}</span>
                </li>
            </ul>
            @php
                $apartmentsWithLicenses = $apartments->filter(fn ($apt) => filled($apt->license_cir) || filled($apt->license_cin));
            @endphp
            @if($apartmentsWithLicenses->isNotEmpty())
            <div class="mt-10 border-t border-stone-200 pt-8">
                <h3 class="text-[11px] font-semibold uppercase tracking-[0.22em] text-gold-600">{{ __('Tourist rental identification') }}</h3>
                <dl class="mt-4 space-y-5 text-sm text-stone-700">
                    @foreach($apartmentsWithLicenses as $apt)
                    <div>
                        <dt class="font-semibold text-lake-950">
                            <a href="{{ localized_route('apartments.show', ['apartment' => $apt]) }}" class="transition hover:text-lake-800 hover:underline">{{ $apt->name }}</a>
                        </dt>
                        <dd class="mt-2 space-y-1">
                            @if(filled($apt->license_cir))
                            <p><span class="text-stone-500">{{ __('CIR') }}:</span> <span class="font-mono text-[0.9375rem] tabular-nums text-lake-950">{{ $apt->license_cir }}</span></p>
                            @endif
                            @if(filled($apt->license_cin))
                            <p><span class="text-stone-500">{{ __('CIN') }}:</span> <span class="font-mono text-[0.9375rem] tabular-nums text-lake-950">{{ $apt->license_cin }}</span></p>
                            @endif
                        </dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif
            @if(config('lakegarda.map_embed_url'))
            <div class="mt-8 aspect-video overflow-hidden rounded-2xl bg-stone-200">
                <iframe src="{{ config('lakegarda.map_embed_url') }}" class="h-full w-full border-0" title="Map" loading="lazy"></iframe>
            </div>
            @endif
        </div>
        <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-sm">
            <h2 class="font-display text-xl font-semibold text-lake-950">{{ __('Booking request') }}</h2>
            <x-inquiry-form
                :action-url="localized_route('contact.store')"
                :apartments="$apartments"
                source-page="contact"
                :submit-label="__('Send message')"
            />
        </div>
    </div>
</section>
@endsection
