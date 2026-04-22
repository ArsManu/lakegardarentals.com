@extends('layouts.admin')

@section('title', __('Home hero slideshow').' — '.$page->slug)

@section('content')
<div class="max-w-4xl">
    <p class="text-sm text-stone-500">
        <a href="{{ route('admin.pages.edit', $page) }}" class="font-medium text-lake-800 hover:underline">{{ __('← Back to Home page') }}</a>
    </p>
    <h1 class="mt-2 font-display text-2xl font-semibold text-lake-950">{{ __('Home hero slideshow') }}</h1>
    <p class="mt-1 text-sm text-stone-600">{{ __('Edit the rotating slides at the top of the homepage. Fill copy and buttons on slide 1 first—later slides reuse that text when their fields are left empty.') }}</p>

    @include('admin.partials.translate-languages-form', ['type' => 'page', 'id' => $page->id])

    <form method="post" action="{{ route('admin.pages.hero-slideshow.update', $page) }}" enctype="multipart/form-data" class="mt-8 space-y-6" data-admin-dirty-form>
        @csrf
        @method('PUT')
        @include('admin.pages._blocks-home-hero-slideshow-form', ['blocks' => $blocks ?? []])

        <button type="submit" class="js-primary-save rounded-full bg-lake-900 px-6 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">{{ __('Save slideshow') }}</button>
    </form>
</div>
@endsection
