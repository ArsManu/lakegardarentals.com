@extends('layouts.admin')

@section('title', __('Edit page').' — '.$page->slug)

@section('content')
<h1 class="font-display text-2xl font-semibold text-lake-950">{{ __('Edit page: :slug', ['slug' => $page->slug]) }}</h1>

@include('admin.partials.translate-languages-form', ['type' => 'page', 'id' => $page->id])

<form method="post" action="{{ route('admin.pages.update', $page) }}" enctype="multipart/form-data" class="mt-8 max-w-4xl space-y-6" data-admin-dirty-form>
    @csrf
    @method('PUT')
    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-medium">{{ __('Please fix the highlighted fields and try again.') }}</p>
        </div>
    @endif
    <div>
        <label class="block text-sm font-medium text-stone-700">{{ __('Title') }}</label>
        <input type="text" name="title" value="{{ old('title', $page->title) }}" required class="mt-1 w-full rounded-lg border-stone-300">
        @error('title')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-stone-700">Meta title</label>
            <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700">Canonical URL</label>
            <input type="url" name="canonical_url" value="{{ old('canonical_url', $page->canonical_url) }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-stone-700">Meta description</label>
        <textarea name="meta_description" rows="3" class="mt-1 w-full rounded-lg border-stone-300">{{ old('meta_description', $page->meta_description) }}</textarea>
    </div>
    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-stone-700">OG title</label>
            <input type="text" name="og_title" value="{{ old('og_title', $page->og_title) }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
        <div>
            <label class="block text-sm font-medium text-stone-700">OG description</label>
            <textarea name="og_description" rows="2" class="mt-1 w-full rounded-lg border-stone-300">{{ old('og_description', $page->og_description) }}</textarea>
        </div>
    </div>

    @includeWhen(view()->exists('admin.pages.blocks-'.$page->slug), 'admin.pages.blocks-'.$page->slug, ['blocks' => $blocks ?? [], 'page' => $page])

    <button type="submit" class="js-primary-save rounded-full bg-lake-900 px-6 py-2 text-sm font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">{{ __('Save') }}</button>
</form>
@endsection
