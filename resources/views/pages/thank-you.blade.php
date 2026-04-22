@extends('layouts.site')

@push('meta')
<x-seo-meta title="{{ __('Thank you') }}" :noindex="true" />
@endpush

@section('content')
<section class="site-header-clear mx-auto max-w-2xl px-4 pb-24 text-center lg:px-8">
    <h1 class="font-display text-4xl font-semibold text-lake-950">{{ __('Thank you') }}</h1>
    <p class="mt-6 text-lg text-stone-600">{{ __('We have received your message and will get back to you shortly.') }}</p>
    <a href="{{ localized_route('home') }}" class="mt-10 inline-block rounded-full bg-lake-900 px-8 py-3 text-sm font-semibold text-white">{{ __('Back to home') }}</a>
</section>
@endsection
