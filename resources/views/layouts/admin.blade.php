<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <title>@yield('title', __('Admin')) — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-stone-100 font-sans text-stone-800 antialiased">
    <div class="flex min-h-screen">
        <aside class="hidden w-56 flex-shrink-0 border-r border-stone-200 bg-white lg:block">
            <div class="border-b border-stone-100 px-4 py-5 font-display text-lg font-semibold text-lake-900">{{ config('lakegarda.site_name') }}</div>
            <nav class="space-y-1 p-3 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Dashboard') }}</a>
                <a href="{{ route('admin.apartments.index') }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Apartments') }}</a>
                <a href="{{ route('admin.amenities.index') }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Amenities') }}</a>
                <a href="{{ route('admin.inquiries.index') }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Inquiries') }}</a>
                <a href="{{ route('admin.faqs.index') }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('FAQs') }}</a>
                <a href="{{ route('admin.testimonials.index') }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Testimonials') }}</a>
                <p class="px-3 pt-4 text-xs font-semibold uppercase text-stone-400">{{ __('Pages') }}</p>
                <a href="{{ route('admin.pages.edit', ['page' => 'home']) }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Home') }}</a>
                <a href="{{ route('admin.pages.hero-slideshow.edit', ['page' => 'home']) }}" class="block rounded-lg px-3 py-2 pl-6 text-stone-600 hover:bg-stone-50">{{ __('Home hero slideshow') }}</a>
                <a href="{{ route('admin.pages.edit', ['page' => 'lake-garda']) }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Lake Garda') }}</a>
                <a href="{{ route('admin.pages.edit', ['page' => 'contact']) }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Contact') }}</a>
                <a href="{{ route('admin.pages.edit', ['page' => 'apartments']) }}" class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Apartments listing') }}</a>
            </nav>
        </aside>
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between border-b border-stone-200 bg-white px-4 py-3">
                <span class="text-sm text-stone-500">{{ auth()->user()->email }}</span>
                <div class="flex items-center gap-3">
                    <a href="{{ route('home') }}" target="_blank" class="text-sm text-lake-800 hover:underline">{{ __('View site') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-stone-600 hover:text-lake-900">{{ __('Log out') }}</button>
                    </form>
                </div>
            </header>
            <main class="flex-1 p-4 lg:p-8">
                @if(session('success'))
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900" role="alert">
                        <p class="font-medium">{{ __('Something went wrong') }}</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
