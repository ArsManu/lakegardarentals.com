@extends('layouts.admin')
@section('title', __('Edit testimonial'))
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ __('Edit testimonial') }}</h1>
@include('admin.partials.translate-languages-form', ['type' => 'testimonial', 'id' => $testimonial->id])
<form method="post" action="{{ route('admin.testimonials.update', $testimonial) }}" class="mt-8 max-w-2xl space-y-4" data-admin-dirty-form>
    @csrf
    @method('PUT')
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium">{{ __('Author') }} *</label>
            <input type="text" name="author_name" value="{{ old('author_name', $testimonial->author_name) }}" required class="mt-1 w-full rounded-lg border-stone-300">
        </div>
        <div>
            <label class="block text-sm font-medium">{{ __('Location') }}</label>
            <input type="text" name="author_location" value="{{ old('author_location', $testimonial->author_location) }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
    </div>
    @include('admin.pages._quill', [
        'name' => 'quote',
        'label' => __('Quote').' *',
        'value' => old('quote', $testimonial->quote),
        'minHeightClass' => 'min-h-[180px]',
        'required' => true,
    ])
    <div>
        <label class="block text-sm font-medium">{{ __('Rating') }}</label>
        <input type="number" name="rating" min="1" max="5" value="{{ old('rating', $testimonial->rating) }}" class="mt-1 w-24 rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_published" value="1" id="pub" @checked(old('is_published', $testimonial->is_published)) class="rounded border-stone-300">
        <label for="pub">{{ __('Published') }}</label>
    </div>
    <button type="submit" class="js-primary-save rounded-full bg-lake-900 px-6 py-2 text-sm text-white disabled:cursor-not-allowed disabled:opacity-50">{{ __('Save') }}</button>
</form>
<form method="post" action="{{ route('admin.testimonials.destroy', $testimonial) }}" class="mt-8" onsubmit="return confirm('Delete?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-sm text-red-600">{{ __('Delete') }}</button>
</form>
@endsection
