@props(['variant' => 'bar'])

@php
    $localeAlts = localized_alternates();
    $localeNames = [
        'en' => 'English',
        'de' => 'Deutsch',
        'it' => 'Italiano',
    ];
    $localeShort = [
        'en' => 'EN',
        'de' => 'DE',
        'it' => 'IT',
    ];
    $current = (string) app()->getLocale();
    $isDrawer = $variant === 'drawer';
    $isFooter = $variant === 'footer';
    $isBar = $variant === 'bar';
    $useFullLabel = $isFooter;
    $labelId = 'site-lang-'.$variant;
    $listboxId = 'site-lang-list-'.$variant;
    $menuItems = [];
    foreach (config('locales.supported', ['en', 'de', 'it']) as $loc) {
        $menuItems[] = [
            'loc' => $loc,
            'url' => $localeAlts[$loc] ?? url('/'),
            'label' => $useFullLabel ? ($localeNames[$loc] ?? strtoupper($loc)) : ($localeShort[$loc] ?? strtoupper($loc)),
            'current' => $current === $loc,
        ];
    }
    $currentLabel = $useFullLabel
        ? ($localeNames[$current] ?? strtoupper($current))
        : ($localeShort[$current] ?? strtoupper($current));
@endphp

<div
    @class([
        'relative',
        'hidden min-w-0 sm:flex sm:items-center' => $isBar,
        'w-full' => $isDrawer,
        'w-full max-w-[14rem] sm:max-w-[16rem]' => $isFooter,
    ])
>
    <p id="{{ $labelId }}" class="sr-only">{{ __('Language') }}</p>
    <div
        @class([
            'relative flex min-w-0 max-w-full items-center overflow-visible border focus-within:ring-2 focus-within:ring-lake-500/30 focus-within:ring-offset-0',
            'h-8 rounded-full' => $isBar,
            'min-h-[2.75rem] rounded-2xl py-0.5' => $isDrawer,
            'min-h-[2.75rem] rounded-xl py-0.5' => $isFooter,
            'border-stone-200/90 bg-stone-100/90 pl-1.5 pr-1 shadow-sm' => $isFooter,
        ])
        @if (! $isFooter)
            :class="scrolled
                ? 'border-stone-200/90 bg-stone-50/95 pl-1.5 pr-1 shadow-sm'
                : 'border-white/25 bg-white/10 pl-1.5 pr-1 shadow-sm backdrop-blur-sm'"
        @endif
    >
        <div
            @class([
                'pointer-events-none flex shrink-0 select-none items-center justify-center self-center',
                'h-4 w-4' => $isBar,
                'h-5 w-5' => $isDrawer,
                'h-4 w-4 text-lake-800' => $isFooter,
            ])
            @if (! $isFooter)
                :class="scrolled ? 'text-lake-800' : 'text-white/90'"
            @endif
            aria-hidden="true"
        >
            <svg class="h-full w-full" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
            </svg>
        </div>
        <span
            @if($isFooter)
                class="mx-0.5 h-4 w-px shrink-0 self-center bg-stone-300/80"
            @else
                class="mx-0.5 h-4 w-px shrink-0 self-center"
                :class="scrolled ? 'bg-stone-200/80' : 'bg-white/30'"
            @endif
        ></span>
        <div
            @class(['relative min-w-0 flex-1', 'text-lake-950' => $isFooter])
            @if (! $isFooter)
                :class="scrolled ? 'text-lake-950' : 'text-white'"
            @endif
        >
            <div
                class="relative w-full"
                @if ($isBar)
                    @click.outside="langOpenBar = false"
                @elseif ($isDrawer)
                    @click.outside="langOpenDrawer = false"
                @else
                    x-data="{ open: false }"
                    @click.outside="open = false"
                @endif
            >
                <button
                    type="button"
                    class="flex w-full min-w-0 items-center justify-between gap-0.5 text-left font-semibold text-inherit focus:outline-none focus-visible:ring-2 focus-visible:ring-lake-500/50 focus-visible:ring-offset-0"
                    @class([
                        'h-8 pl-0.5 pr-0.5 text-xs tabular-nums leading-none tracking-wide' => $isBar,
                        'min-h-[2.5rem] py-0 pl-0.5 pr-0.5 text-sm' => $isDrawer,
                        'min-h-[2.5rem] py-0 pl-0.5 pr-0.5 text-sm' => $isFooter,
                    ])
                    aria-haspopup="listbox"
                    @if ($isBar)
                        :aria-expanded="langOpenBar"
                        @click="langOpenBar = !langOpenBar; langOpenDrawer = false"
                        @keydown.escape.prevent="langOpenBar = false"
                    @elseif ($isDrawer)
                        :aria-expanded="langOpenDrawer"
                        @click="langOpenDrawer = !langOpenDrawer; langOpenBar = false"
                        @keydown.escape.prevent="langOpenDrawer = false"
                    @else
                        :aria-expanded="open"
                        @click="open = !open"
                        @keydown.escape.prevent="open = false"
                    @endif
                    aria-controls="{{ $listboxId }}"
                    title="{{ __('Language') }}: {{ $currentLabel }}"
                >
                    <span class="min-w-0 truncate" id="{{ $labelId }}-value">{{ $currentLabel }}</span>
                    <span
                        @class(['inline-flex shrink-0 items-center justify-center', 'h-4 w-4' => $isBar, 'h-5 w-5' => $isFooter || $isDrawer, 'text-lake-600' => $isFooter])
                        @if (! $isFooter)
                            :class="scrolled ? 'text-lake-600' : 'text-white/90'"
                        @endif
                    >
                        <svg
                            @class(['transition-transform duration-200', 'h-2.5 w-2.5' => $isBar, 'h-3 w-3' => $isFooter || $isDrawer])
                            @if ($isBar)
                                :class="langOpenBar ? 'rotate-180' : ''"
                            @elseif ($isDrawer)
                                :class="langOpenDrawer ? 'rotate-180' : ''"
                            @else
                                :class="open ? 'rotate-180' : ''"
                            @endif
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                        </svg>
                    </span>
                </button>

                <ul
                    id="{{ $listboxId }}"
                    role="listbox"
                    @if ($isBar)
                        x-show="langOpenBar"
                    @elseif ($isDrawer)
                        x-show="langOpenDrawer"
                    @else
                        x-show="open"
                    @endif
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 -translate-y-0.5 scale-[0.98]"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @if ($isBar)
                        class="absolute right-0 z-[60] mt-1.5 min-w-full max-w-[12rem] overflow-hidden rounded-xl border border-stone-200/90 bg-white py-1 shadow-xl ring-1 ring-black/5"
                    @elseif ($isDrawer)
                        class="absolute left-0 right-0 z-[60] mt-1.5 overflow-hidden rounded-2xl border border-stone-200/90 bg-white py-1.5 shadow-xl ring-1 ring-black/5"
                    @else
                        class="absolute left-0 right-0 z-[60] mt-1.5 overflow-hidden rounded-xl border border-stone-200/90 bg-white py-1.5 shadow-xl ring-1 ring-black/5"
                    @endif
                >
                    @foreach ($menuItems as $item)
                        <li role="option" aria-selected="{{ $item['current'] ? 'true' : 'false' }}">
                            <a
                                href="{{ $item['url'] }}"
                                @if ($isBar)
                                    @click="langOpenBar = false"
                                @elseif ($isDrawer)
                                    @click="langOpenDrawer = false; mobileOpen = false"
                                @else
                                    @click="open = false"
                                @endif
                                @class([
                                    'flex items-center justify-between gap-2 px-3 py-2.5 text-sm font-medium text-lake-900 transition',
                                    'bg-lake-50' => $item['current'],
                                    'hover:bg-stone-50' => ! $item['current'],
                                ])
                            >
                                <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                                @if($item['current'])
                                    <svg class="h-4 w-4 shrink-0 text-gold-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
