@props([
    'slides' => [],
])

@php
    use App\Support\HtmlTranslationSanity;
    use App\Support\MediaUrl;

    /** @var array<int, mixed> $allSlides */
    $allSlides = is_array($slides) ? array_values($slides) : [];
    $firstSlide = ($allSlides[0] ?? null) && is_array($allSlides[0]) ? $allSlides[0] : [];

    $nonEmpty = static fn (?string $s): bool => trim((string) $s) !== '';

    $fieldOrFirst = static function (int $slotIndex, array $slide, array $first, string $key) use ($nonEmpty): string {
        $v = (string) ($slide[$key] ?? '');
        if ($slotIndex === 0) {
            return $v;
        }

        return $nonEmpty($v) ? $v : (string) ($first[$key] ?? '');
    };

    /** Rich-text fields (Quill): treat tag-only / whitespace HTML as empty so slide 2+ can fall back to slide 1 like plain-text fields. */
    $htmlOrFirst = static function (int $slotIndex, array $slide, array $first, string $key): string {
        $v = (string) ($slide[$key] ?? '');
        if ($slotIndex === 0) {
            return $v;
        }
        $plain = trim(html_entity_decode(strip_tags($v), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        return $plain !== '' ? $v : (string) ($first[$key] ?? '');
    };

    $ctaOrFirst = static function (int $slotIndex, array $slide, array $first, string $labelKey, string $urlKey) use ($nonEmpty): array {
        $sL = trim((string) ($slide[$labelKey] ?? ''));
        $sU = trim((string) ($slide[$urlKey] ?? ''));
        $slideOk = $sL !== '' && $sU !== '';
        if ($slotIndex === 0) {
            return $slideOk ? [$sL, $sU] : ['', ''];
        }
        if ($slideOk) {
            return [$sL, $sU];
        }
        $fL = trim((string) ($first[$labelKey] ?? ''));
        $fU = trim((string) ($first[$urlKey] ?? ''));
        $firstOk = $fL !== '' && $fU !== '';

        return $firstOk ? [$fL, $fU] : ['', ''];
    };

    $slidesWithImages = [];
    foreach ($allSlides as $slotIndex => $slide) {
        if (! is_array($slide)) {
            continue;
        }
        if (($slide['image_path'] ?? '') === '') {
            continue;
        }
        $slidesWithImages[] = ['slide' => $slide, 'slot' => $slotIndex];
    }
@endphp

@if(count($slidesWithImages) > 0)
    <section class="home-hero-slider relative isolate w-full max-w-none overflow-hidden bg-lake-950">
        <div class="swiper w-full max-w-full" data-home-hero-slider>
            <div class="swiper-wrapper">
                @foreach($slidesWithImages as $item)
                    @php
                        $slotIndex = $item['slot'];
                        $slide = $item['slide'];
                        $slideSrc = MediaUrl::public($slide['image_path'] ?? '');
                        $eyebrow = $fieldOrFirst($slotIndex, $slide, $firstSlide, 'eyebrow');
                        $title = $fieldOrFirst($slotIndex, $slide, $firstSlide, 'title');
                        $subtitle = HtmlTranslationSanity::toDisplayableHtml($htmlOrFirst($slotIndex, $slide, $firstSlide, 'subtitle'));
                        [$primaryLabel, $primaryUrl] = $ctaOrFirst($slotIndex, $slide, $firstSlide, 'primary_cta_label', 'primary_cta_url');
                        [$secondaryLabel, $secondaryUrl] = $ctaOrFirst($slotIndex, $slide, $firstSlide, 'secondary_cta_label', 'secondary_cta_url');
                        $imageAlt = ($slide['image_alt'] ?? '') !== '' ? $slide['image_alt'] : $title;
                    @endphp
                    <div class="swiper-slide relative w-full max-w-full shrink-0 overflow-hidden">
                        <div class="absolute inset-0">
                            <div
                                class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                                style="background-image: url('{{ $slideSrc }}');"
                                role="img"
                                aria-label="{{ $imageAlt }}"
                            ></div>
                            <div
                                class="pointer-events-none absolute inset-0"
                                style="background-image:
                                    radial-gradient(ellipse 120% 90% at 18% 88%, rgba(0,0,0,0.48) 0%, rgba(0,0,0,0.14) 42%, transparent 62%),
                                    linear-gradient(to top, rgba(0,0,0,0.52) 0%, rgba(0,0,0,0.18) 32%, rgba(0,0,0,0.05) 52%, rgba(0,0,0,0.02) 65%, transparent 78%),
                                    linear-gradient(to right, rgba(0,0,0,0.28) 0%, rgba(0,0,0,0.06) 45%, transparent 72%);"
                                aria-hidden="true"
                            ></div>
                        </div>

                        <div class="home-hero-slide-content relative mx-auto flex max-w-7xl items-end px-4 pb-20 pt-40 sm:px-6 lg:px-8 lg:pt-44 lg:pb-24">
                            <div class="max-w-3xl">
                                <div class="relative">
                                @if($eyebrow !== '')
                                    <p class="text-xs font-semibold uppercase tracking-[0.34em] text-white sm:text-sm">{{ $eyebrow }}</p>
                                @endif
                                <h1 class="mt-4 max-w-3xl font-display text-5xl font-bold leading-[0.9] text-white drop-shadow-[0_12px_36px_rgba(0,0,0,0.55)] sm:text-6xl lg:text-7xl">
                                    {{ $title }}
                                </h1>
                                <div class="prose prose-invert prose-lg mt-5 max-w-2xl text-white [&_*]:!text-white [&_a]:!text-white/95 prose-p:my-0 prose-p:text-lg prose-p:leading-8 prose-a:underline-offset-2 drop-shadow-[0_6px_18px_rgba(0,0,0,0.45)] md:prose-xl md:prose-p:leading-8">{!! $subtitle !!}</div>

                                @if(($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== ''))
                                    <div class="mt-8 flex flex-wrap gap-4">
                                        @if($primaryLabel !== '' && $primaryUrl !== '')
                                            <a href="{{ $primaryUrl }}" class="inline-flex items-center justify-center rounded-full bg-white px-8 py-5 text-base font-semibold leading-none text-lake-900 shadow-lg">{{ $primaryLabel }}</a>
                                        @endif
                                        @if($secondaryLabel !== '' && $secondaryUrl !== '')
                                            <a href="{{ $secondaryUrl }}" class="inline-flex items-center justify-center rounded-full border-2 border-white px-8 py-5 text-base font-semibold leading-none text-white shadow-sm">{{ $secondaryLabel }}</a>
                                        @endif
                                    </div>
                                @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
