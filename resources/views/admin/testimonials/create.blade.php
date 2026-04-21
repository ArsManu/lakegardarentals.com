@extends('layouts.admin')
@section('title', __('New testimonial'))
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ __('New testimonial') }}</h1>
<form method="post" action="{{ route('admin.testimonials.store') }}" class="mt-8 max-w-2xl space-y-4">
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium">{{ __('Author') }} *</label>
            <input type="text" name="author_name" value="{{ old('author_name') }}" required class="mt-1 w-full rounded-lg border-stone-300">
        </div>
        <div>
            <label class="block text-sm font-medium">{{ __('Location') }}</label>
            <input type="text" name="author_location" value="{{ old('author_location') }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
    </div>
    @include('admin.pages._quill', [
        'name' => 'quote',
        'label' => __('Quote').' *',
        'value' => old('quote', ''),
        'minHeightClass' => 'min-h-[180px]',
        'required' => true,
    ])
    <div>
        <label class="block text-sm font-medium">{{ __('Rating') }} 1–5</label>
        <input type="number" name="rating" min="1" max="5" value="{{ old('rating', 5) }}" class="mt-1 w-24 rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_published" value="1" id="pub" checked class="rounded border-stone-300">
        <label for="pub">{{ __('Published') }}</label>
    </div>
    <button type="submit" class="rounded-full bg-lake-900 px-6 py-2 text-sm text-white">{{ __('Create') }}</button>
</form>
@endsection
