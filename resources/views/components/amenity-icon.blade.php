@props(['amenity'])
@php
    /** @var \App\Models\Amenity $amenity */
    $k = $amenity->icon_key ? strtolower($amenity->icon_key) : null;
    if (! $k) {
        $n = strtolower(preg_replace('/[\p{Pd}\']/u', '-', $amenity->name));
        $k = match (true) {
            str_contains($n, 'wi-fi') || str_contains($n, 'wifi') => 'wifi',
            str_contains($n, 'air') && str_contains($n, 'condition') => 'air-conditioning',
            str_contains($n, 'kitchen') => 'kitchen',
            str_contains($n, 'parking') => 'parking',
            str_contains($n, 'lake') && str_contains($n, 'view') => 'lake-view',
            str_contains($n, 'washing') => 'washing-machine',
            str_contains($n, 'dishwasher') => 'dishwasher',
            str_contains($n, 'balcony') || str_contains($n, 'terrace') => 'balcony',
            str_contains($n, 'coffee') => 'coffee',
            str_contains($n, 'tv') || str_contains($n, 'smart') => 'tv',
            default => 'sparkles',
        };
    }
@endphp
<svg {{ $attributes->class(['h-6 w-6 flex-shrink-0 sm:h-7 sm:w-7']) }} fill="none" stroke="currentColor" stroke-width="1.625" viewBox="0 0 24 24" aria-hidden="true">
@switch($k)
    @case('wifi')
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.036a5.25 5.25 0 017.424 0M5.106 11.893c3.31-3.31 8.686-3.31 11.996 0M1.757 8.655c4.687-4.687 12.284-4.687 16.971 0M12.75 21.75h.008v.008H12.75V21.75z"/>
        @break
    @case('air-conditioning')
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
        @break
    @case('kitchen')
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
        @break
    @case('parking')
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 8.25h13.5A2.25 2.25 0 0021 18.75V8.25A2.25 2.25 0 0018.75 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21zm0 0h18"/>
        @break
    @case('lake-view')
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        @break
    @case('washing-machine')
        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/>
        @break
    @case('dishwasher')
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/>
        @break
    @case('balcony')
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008H17.25v-.008zm0-3.75h.008v.008H17.25V9z"/>
        @break
    @case('coffee')
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M4.927 20.896a48.486 48.486 0 01-.75-.082m0 0a24.301 24.301 0 01-4.5 0m4.5 0a24.301 24.301 0 01-4.5 0"/>
        @break
    @case('tv')
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 20.25h12m-7.5-3v3m3-3v3m-10-15h18a1.5 1.5 0 011.5 1.5v9a1.5 1.5 0 01-1.5 1.5h-18a1.5 1.5 0 01-1.5-1.5v-9a1.5 1.5 0 011.5-1.5z"/>
        @break
    @default
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
@endswitch
</svg>
