@props([
    'imageSrc' => '',
    'title' => '',
    'titleFallback' => '',
    'subtitle' => '',
    'subtitleFallback' => '',
    'pageLabel' => '',
])

@php
    use App\Support\HtmlTranslationSanity;
    $hasImage = ($imageSrc ?? '') !== '';
    $hasTitle = is_string($title) && trim($title) !== '';
    $hasSubtitle = is_string($subtitle) && trim($subtitle) !== '';
    $displayTitle = trans_page_string($hasTitle ? trim($title) : null, (string) ($titleFallback ?? ''));
    $rawSubtitle = trans_page_string($hasSubtitle ? trim($subtitle) : null, (string) ($subtitleFallback ?? ''));
    $safeSubtitle = HtmlTranslationSanity::toDisplayableHtml($rawSubtitle);
@endphp

{{--
  Inner page hero (all CMS inner heroes except home): fixed 400px band, home-style scrims, content
  anchored toward the bottom. Home keeps full-viewport home-hero-slider.
--}}
<section
    class="site-header-clear relative isolate flex h-[400px] min-h-[400px] flex-col overflow-hidden text-white {{ $hasImage ? '' : 'bg-lake-950' }}"
>
    @if($hasImage)
        <div
            class="pointer-events-none absolute inset-0 bg-cover bg-center bg-no-repeat"
            style="background-image: url('{{ $imageSrc }}');"
            aria-hidden="true"
        ></div>
        {{-- Same scrim family as components/home-hero-slider + extra top band for fixed header --}}
        <div
            class="pointer-events-none absolute inset-0"
            style="background-image:
                linear-gradient(to bottom, rgba(15,23,42,0.58) 0%, rgba(15,23,42,0.24) 22%, rgba(15,23,42,0.07) 46%, transparent 64%),
                radial-gradient(ellipse 120% 90% at 18% 88%, rgba(0,0,0,0.48) 0%, rgba(0,0,0,0.14) 42%, transparent 62%),
                linear-gradient(to top, rgba(0,0,0,0.52) 0%, rgba(0,0,0,0.18) 32%, rgba(0,0,0,0.05) 52%, rgba(0,0,0,0.02) 65%, transparent 78%),
                linear-gradient(to right, rgba(0,0,0,0.28) 0%, rgba(0,0,0,0.06) 45%, transparent 72%);"
            aria-hidden="true"
        ></div>
    @else
        <div class="pointer-events-none absolute inset-0 bg-gradient-to-br from-lake-800 via-lake-950 to-stone-950" aria-hidden="true"></div>
        <div
            class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,255,255,0.14),transparent_42%)]"
            aria-hidden="true"
        ></div>
    @endif

    {{-- Same horizontal band as site-header: mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 --}}
    <div class="relative z-10 mx-auto flex w-full max-w-7xl flex-1 flex-col justify-end px-4 pb-20 pt-8 sm:px-6 lg:px-8 lg:pb-28">
        <div class="w-full min-w-0">
            <nav class="text-sm" aria-label="{{ __('Breadcrumb') }}">
                <a
                    href="{{ localized_route('home') }}"
                    class="{{ $hasImage ? 'text-stone-200 hover:text-white' : 'text-stone-400 hover:text-white' }}"
                >{{ __('Home') }}</a>
                <span class="mx-2 {{ $hasImage ? 'text-stone-400' : 'text-stone-500' }}">/</span>
                <span class="{{ $hasImage ? 'font-medium text-white' : 'text-stone-200' }}">{{ $pageLabel }}</span>
            </nav>
            <h1
                class="mt-6 w-full max-w-none font-display text-5xl font-bold leading-[0.95] tracking-tight text-white drop-shadow-[0_12px_36px_rgba(0,0,0,0.5)] sm:text-6xl lg:text-7xl"
            >{{ $displayTitle }}</h1>
            @if($safeSubtitle !== '')
                <div class="prose prose-invert prose-lg mt-5 w-full max-w-none text-white [&_*]:!text-white [&_a]:!text-white/95 prose-p:my-0 prose-p:leading-relaxed prose-a:underline-offset-2 drop-shadow-[0_6px_18px_rgba(0,0,0,0.4)] md:prose-xl">{!! $safeSubtitle !!}</div>
            @endif
        </div>
    </div>
</section>
