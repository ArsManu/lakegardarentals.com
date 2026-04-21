@php
    // Home: transparent header over the hero until scroll. All other pages: solid white bar by default.
    $headerSolid = $headerSolid ?? ! request()->routeIs('home');
    $wa = $siteWhatsapp ?? null;
    $waNumber = $wa ?: ($sitePhoneTel ?? null);
    $waUrl = $waNumber ? 'https://wa.me/'.preg_replace('/\D/', '', $waNumber) : null;
    $name = trim((string) $siteName);
    $wordCount = $name === '' ? 0 : count(preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY));
    if ($wordCount > 2) {
        $nameParts = preg_split('/\s+(?=[^\s]+$)/u', $name, 2);
        $logoPrimary = $nameParts[0] ?? $name;
        $logoSecondary = $nameParts[1] ?? null;
    } else {
        $logoPrimary = $name;
        $logoSecondary = null;
    }
@endphp
<a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:z-[100] focus:p-4 focus:bg-white focus:text-lake-900">{{ __('Skip to content') }}</a>
<header
    x-data="{ scrolled: @json($headerSolid), forceSolid: @json($headerSolid), mobileOpen: false }"
    @scroll.window="if (!forceSolid) scrolled = window.scrollY > 12"
    class="fixed left-0 right-0 top-0 z-50 transition-[background-color,box-shadow,backdrop-filter] duration-300 ease-out {{ $headerSolid ? 'bg-white shadow-sm backdrop-blur-none' : '' }}"
    :class="scrolled ? 'bg-white shadow-sm backdrop-blur-none' : 'bg-lake-950/40 backdrop-blur-xl'"
>
    <div class="relative mx-auto flex max-w-7xl items-center justify-between gap-3 px-4 py-4 sm:px-6 sm:py-[1.125rem] lg:gap-8 lg:px-8 lg:py-5">
        {{-- Logo lockup (footer style) --}}
        <a
            href="{{ route('home') }}"
            class="group relative shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-gold-500/50 focus-visible:ring-offset-2"
            :class="scrolled ? 'focus-visible:ring-offset-white' : 'focus-visible:ring-offset-transparent'"
        >
            <span class="flex flex-col leading-none">
                <span
                    class="font-display text-[1.35rem] font-semibold tracking-tight transition-colors sm:text-2xl md:text-[1.65rem]"
                    :class="scrolled ? 'text-lake-950 group-hover:text-lake-800' : 'text-white group-hover:text-white/90'"
                >
                    {{ $logoPrimary }}@if($logoSecondary !== null)<span class="font-normal transition-colors" :class="scrolled ? 'text-lake-800 group-hover:text-lake-700' : 'text-white/85 group-hover:text-white/80'"> {{ $logoSecondary }}</span>@endif
                </span>
                <span
                    class="mt-1.5 h-px w-8 bg-gradient-to-r transition-all duration-300 group-hover:w-12"
                    :class="scrolled ? 'from-gold-600 to-gold-500/35' : 'from-gold-500/90 to-gold-500/25'"
                    aria-hidden="true"
                ></span>
            </span>
            <span class="sr-only">{{ $siteName }} — {{ __('Home') }}</span>
        </a>

        {{-- Desktop nav --}}
        <nav class="hidden items-center gap-0.5 lg:flex lg:gap-1" aria-label="{{ __('Primary') }}">
            @foreach ([
                ['route' => 'home', 'label' => __('Home'), 'match' => ['home']],
                ['route' => 'lake-garda', 'label' => __('Lake Garda'), 'match' => ['lake-garda']],
                ['route' => 'apartments.index', 'label' => __('Apartments'), 'match' => ['apartments.index', 'apartments.show']],
                ['route' => 'contact', 'label' => __('Contact'), 'match' => ['contact']],
            ] as $item)
                @php $active = request()->routeIs(...$item['match']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    class="group relative rounded-lg px-3.5 py-2 text-sm font-semibold uppercase tracking-[0.12em] transition-colors lg:text-[0.9375rem] lg:tracking-[0.11em]"
                    :class="scrolled
                        ? 'text-lake-950 {{ $active ? '' : 'hover:bg-black/[0.06]' }}'
                        : 'text-white {{ $active ? '' : 'hover:bg-white/10' }}'"
                >
                    <span class="relative z-10">{{ $item['label'] }}</span>
                    @if($active)
                        <span class="absolute inset-x-2 -bottom-px h-0.5 rounded-full bg-gradient-to-r from-gold-500 to-gold-500/40" aria-hidden="true"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- CTAs --}}
        <div class="flex items-center gap-1.5 sm:gap-2">
            {{-- Desktop: phone pill + WhatsApp icon (same bar; separate links) --}}
            <div
                class="hidden overflow-hidden rounded-full shadow-sm transition hover:shadow-md lg:inline-flex"
                :class="scrolled ? 'bg-lake-950 text-white' : 'bg-white text-lake-950'"
            >
                <a
                    href="tel:{{ $sitePhoneTel }}"
                    class="inline-flex items-center gap-2.5 px-5 py-3 text-base font-semibold tabular-nums tracking-wide transition-colors"
                    :class="scrolled ? 'hover:bg-lake-900' : 'hover:bg-stone-100'"
                >
                    <svg class="h-5 w-5 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" :class="scrolled ? 'text-white' : 'text-lake-900'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                    </svg>
                    {{ $sitePhone }}
                </a>
                @if($waUrl)
                    <a
                        href="{{ $waUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex shrink-0 items-center justify-center border-l px-3.5 py-3 transition-colors"
                        :class="scrolled ? 'border-white/20 hover:bg-lake-900' : 'border-stone-200/90 hover:bg-stone-100'"
                        aria-label="{{ __('WhatsApp') }}"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" :class="scrolled ? 'text-green-400' : 'text-green-600'"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>
                @endif
            </div>

            {{-- Mobile: call icon --}}
            <a
                href="tel:{{ $sitePhoneTel }}"
                class="inline-flex rounded-full border p-2.5 transition lg:hidden"
                :class="scrolled
                    ? 'border-stone-200 bg-white text-lake-950 shadow-sm hover:bg-stone-50'
                    : 'border-white/20 bg-white/10 text-white hover:bg-white/15'"
                aria-label="{{ __('Phone') }}: {{ $sitePhone }}"
            >
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                </svg>
            </a>
            @if($waUrl)
                <a
                    href="{{ $waUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex rounded-full border p-2.5 transition lg:hidden"
                    :class="scrolled
                        ? 'border-stone-200 bg-white text-green-700 shadow-sm hover:bg-stone-50'
                        : 'border-white/20 bg-white/10 text-green-400 hover:bg-white/15'"
                    aria-label="{{ __('WhatsApp') }}"
                >
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.881 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </a>
            @endif

            {{-- Mobile menu toggle --}}
            <button
                type="button"
                class="inline-flex items-center justify-center rounded-full border p-2.5 transition lg:hidden"
                :class="scrolled
                    ? 'border-stone-200 bg-white text-lake-950 shadow-sm hover:bg-stone-50'
                    : 'border-white/20 bg-white/10 text-white hover:bg-white/15'"
                aria-controls="site-nav-mobile"
                :aria-expanded="mobileOpen"
                @click="mobileOpen = !mobileOpen"
            >
                <span class="sr-only">{{ __('Menu') }}</span>
                <svg x-show="!mobileOpen" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                <svg x-show="mobileOpen" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>

    {{-- Mobile panel --}}
    <div
        id="site-nav-mobile"
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="relative border-t lg:hidden"
        :class="scrolled ? 'border-stone-200 bg-white' : 'border-white/10 bg-lake-950/98 backdrop-blur-xl'"
    >
        <nav class="mx-auto flex max-w-7xl flex-col gap-0.5 px-4 py-4 sm:px-6" aria-label="{{ __('Primary') }}">
            @foreach ([
                ['route' => 'home', 'label' => __('Home'), 'match' => ['home']],
                ['route' => 'lake-garda', 'label' => __('Lake Garda'), 'match' => ['lake-garda']],
                ['route' => 'apartments.index', 'label' => __('Apartments'), 'match' => ['apartments.index', 'apartments.show']],
                ['route' => 'contact', 'label' => __('Contact'), 'match' => ['contact']],
            ] as $item)
                @php $active = request()->routeIs(...$item['match']); @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @click="mobileOpen = false"
                    class="rounded-xl px-4 py-3.5 text-base font-semibold uppercase tracking-[0.12em] transition"
                    :class="scrolled
                        ? '{{ $active ? 'bg-stone-100 text-lake-950' : 'text-lake-950 hover:bg-stone-100' }}'
                        : '{{ $active ? 'bg-white/15 text-white' : 'text-white hover:bg-white/10' }}'"
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>
    </div>
</header>
