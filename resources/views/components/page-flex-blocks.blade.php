@props([
    'blocks' => [],
    'wrapperClass' => 'border-y border-stone-200 bg-white py-12',
])

@php
    use App\Support\HtmlTranslationSanity;
    use App\Support\MediaUrl;
    $list = is_array($blocks) ? $blocks : [];
    $flexSectionHeadingClass = 'font-display text-3xl font-semibold tracking-tight text-lake-950 md:text-4xl';
    $flexProseHeadingTweaks = '[&_h2]:font-display [&_h2]:mt-8 [&_h2]:mb-0 [&_h2]:text-3xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_h2]:text-lake-950 md:[&_h2]:text-4xl [&_h3]:font-display [&_h3]:mt-6 [&_h3]:text-2xl [&_h3]:font-semibold [&_h3]:text-lake-950 md:[&_h3]:text-3xl';
    $flexProseBodyRhythm = 'prose-p:my-0 [&_p+p]:mt-5';
@endphp

@if(count($list))
<section class="{{ $wrapperClass }} min-w-0 max-w-full">
    <div class="mx-auto min-w-0 max-w-7xl space-y-12 px-4 sm:px-6 lg:px-8">
        @foreach($list as $block)
            @continue(empty($block['type']))
            @switch($block['type'])
                @case('split_media')
                    @php
                        $layout = ($block['layout'] ?? '') === 'image_right' ? 'image_right' : 'image_left';
                        $img = MediaUrl::public($block['image_path'] ?? $block['image_url'] ?? '');
                        $alt = $block['image_alt'] ?? '';
                        $heading = $block['heading'] ?? '';
                        $body = str_replace(["\u{00A0}", '&nbsp;', '&#160;'], ' ', $block['body_html'] ?? '');
                        $body = HtmlTranslationSanity::toDisplayableHtml($body);
                    @endphp
                    @if($img !== '' || $heading !== '' || $body !== '')
                        <div class="grid items-center gap-8 lg:grid-cols-2 lg:gap-12">
                            <div class="overflow-hidden rounded-2xl shadow-lg {{ $layout === 'image_right' ? 'lg:order-2' : '' }}">
                                @if($img !== '')
                                    <img src="{{ $img }}" alt="{{ $alt }}" class="h-full w-full max-h-[28rem] object-cover" loading="lazy">
                                @endif
                            </div>
                            <div class="mx-auto w-full max-w-2xl {{ $layout === 'image_right' ? 'lg:order-1' : '' }}">
                                @if($heading !== '')
                                    <h2 class="{{ $flexSectionHeadingClass }}">{{ $heading }}</h2>
                                @endif
                                @if($body !== '')
                                    <div class="prose prose-stone prose-lg mt-4 max-w-none {{ $flexProseBodyRhythm }} {{ $flexProseHeadingTweaks }}">{!! $body !!}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                    @break

                @case('full_bleed_image')
                    @php
                        $url = MediaUrl::public($block['image_path'] ?? $block['image_url'] ?? '');
                        $alt = $block['image_alt'] ?? '';
                        $cap = $block['caption'] ?? '';
                    @endphp
                    @if($url !== '')
                        <figure class="mx-auto max-w-7xl">
                            <div class="overflow-hidden rounded-2xl shadow-lg">
                                <img src="{{ $url }}" alt="{{ $alt }}" class="max-h-[32rem] w-full object-cover" loading="lazy">
                            </div>
                            @if($cap !== '')
                                <figcaption class="mt-3 text-center text-sm text-stone-500">{{ $cap }}</figcaption>
                            @endif
                        </figure>
                    @endif
                    @break

                @case('two_images')
                    @php
                        $l = MediaUrl::public($block['left_path'] ?? $block['left_url'] ?? '');
                        $r = MediaUrl::public($block['right_path'] ?? $block['right_url'] ?? '');
                    @endphp
                    @if($l !== '' || $r !== '')
                        <div class="grid gap-6 md:grid-cols-2">
                            @if($l !== '')
                                <div class="overflow-hidden rounded-2xl shadow-md">
                                    <img src="{{ $l }}" alt="{{ $block['left_alt'] ?? '' }}" class="h-64 w-full object-cover md:h-80" loading="lazy">
                                </div>
                            @endif
                            @if($r !== '')
                                <div class="overflow-hidden rounded-2xl shadow-md">
                                    <img src="{{ $r }}" alt="{{ $block['right_alt'] ?? '' }}" class="h-64 w-full object-cover md:h-80" loading="lazy">
                                </div>
                            @endif
                        </div>
                    @endif
                    @break

                @case('rich_text')
                    @php
                        $html = str_replace(["\u{00A0}", '&nbsp;', '&#160;'], ' ', $block['html'] ?? '');
                        $html = HtmlTranslationSanity::toDisplayableHtml($html);
                        $richHeading = trim((string) ($block['heading'] ?? ''));
                    @endphp
                    @if($richHeading !== '' || $html !== '')
                        <div class="mx-auto min-w-0 max-w-7xl">
                            @if($richHeading !== '')
                                <h2 class="{{ $flexSectionHeadingClass }}">{{ $richHeading }}</h2>
                            @endif
                            @if($html !== '')
                                <div class="prose prose-stone prose-lg min-w-0 max-w-none {{ $flexProseBodyRhythm }} [&_iframe]:max-w-full [&_img]:h-auto [&_img]:max-w-full [&_pre]:max-w-full [&_pre]:overflow-x-auto [&_table]:block [&_table]:max-w-full [&_table]:overflow-x-auto [&_a]:break-all {{ $flexProseHeadingTweaks }} {{ $richHeading !== '' ? 'mt-6' : '' }}">{!! $html !!}</div>
                            @endif
                        </div>
                    @endif
                    @break
            @endswitch
        @endforeach
    </div>
</section>
@endif
