<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    {{ $seo ?? '' }}
    @stack('meta')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('schema')
</head>
<body class="overflow-x-clip bg-stone-50 font-sans text-stone-800 antialiased">
    @include('partials.site-header')
    <main id="main-content" class="w-full min-w-0 max-w-full overflow-x-clip">
        @yield('content')
        @unless(request()->routeIs('apartments.show', 'contact'))
            @include('partials.site-pre-footer')
        @endunless
    </main>
    @include('partials.site-footer')
</body>
</html>
