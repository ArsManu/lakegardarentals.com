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
    <div
        class="flex min-h-screen"
        x-data="{ mobileNavOpen: false }"
        @keydown.escape.window="mobileNavOpen = false"
    >
        <aside class="hidden w-56 flex-shrink-0 border-r border-stone-200 bg-white lg:block" aria-label="{{ __('Admin') }}">
            <div class="border-b border-stone-100 px-4 py-5 font-display text-lg font-semibold text-lake-900">{{ config('lakegarda.site_name') }}</div>
            @include('admin.partials.navigation-items', ['closeOnNavigate' => false])
        </aside>
        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between gap-3 border-b border-stone-200 bg-white px-4 py-3">
                <div class="flex min-w-0 flex-1 items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex rounded-lg border border-stone-200 p-2 text-lake-900 transition hover:bg-stone-50 lg:hidden"
                        @click="mobileNavOpen = true"
                        :aria-expanded="mobileNavOpen"
                        aria-controls="admin-mobile-nav"
                        aria-label="{{ __('Open menu') }}"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                    <span class="min-w-0 truncate text-sm text-stone-500">{{ auth()->user()->email }}</span>
                </div>
                <div class="flex shrink-0 items-center gap-3">
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
                @if(session('warning'))
                    <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">{{ session('warning') }}</div>
                @endif
                @if(session('status') === 'password-updated')
                    <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ __('Password saved.') }}</div>
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
        <div
            x-show="mobileNavOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 bg-stone-900/50 lg:hidden"
            @click="mobileNavOpen = false"
            id="admin-mobile-nav-backdrop"
            aria-hidden="true"
        ></div>
        <div
            x-show="mobileNavOpen"
            x-cloak
            x-transition:enter="transition transform ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition transform ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-[60] flex w-[min(20rem,88vw)] max-w-full flex-col border-r border-stone-200 bg-white shadow-xl lg:hidden"
            id="admin-mobile-nav"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('Admin menu') }}"
        >
            <div class="flex items-center justify-between border-b border-stone-100 px-3 py-4">
                <span class="pl-1 font-display text-base font-semibold text-lake-900">{{ config('lakegarda.site_name') }}</span>
                <button
                    type="button"
                    class="rounded-lg p-2 text-stone-500 transition hover:bg-stone-100 hover:text-lake-900"
                    @click="mobileNavOpen = false"
                    aria-label="{{ __('Close menu') }}"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="min-h-0 flex-1 overflow-y-auto">
                @include('admin.partials.navigation-items', ['closeOnNavigate' => true])
            </div>
        </div>
    </div>
</body>
</html>
