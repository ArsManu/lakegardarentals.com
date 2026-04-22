@php
    $closeOnNavigate = $closeOnNavigate ?? false;
@endphp
<nav class="space-y-1 p-3 text-sm">
    <a href="{{ route('admin.dashboard') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Dashboard') }}</a>
    <a href="{{ route('admin.settings.edit') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Settings') }}</a>
    <a href="{{ route('admin.apartments.index') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Apartments') }}</a>
    <a href="{{ route('admin.amenities.index') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Amenities') }}</a>
    <a href="{{ route('admin.inquiries.index') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Inquiries') }}</a>
    <a href="{{ route('admin.faqs.index') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('FAQs') }}</a>
    <a href="{{ route('admin.testimonials.index') }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Testimonials') }}</a>
    <p class="px-3 pt-4 text-xs font-semibold uppercase text-stone-400">{{ __('Pages') }}</p>
    <a href="{{ route('admin.pages.edit', ['page' => 'home']) }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Home') }}</a>
    <a href="{{ route('admin.pages.hero-slideshow.edit', ['page' => 'home']) }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 pl-6 text-stone-600 hover:bg-stone-50">{{ __('Home hero slideshow') }}</a>
    <a href="{{ route('admin.pages.edit', ['page' => 'lake-garda']) }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Lake Garda') }}</a>
    <a href="{{ route('admin.pages.edit', ['page' => 'contact']) }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Contact') }}</a>
    <a href="{{ route('admin.pages.edit', ['page' => 'apartments']) }}"
        @if($closeOnNavigate) @click="mobileNavOpen = false" @endif
        class="block rounded-lg px-3 py-2 hover:bg-stone-50">{{ __('Apartments listing') }}</a>
</nav>
