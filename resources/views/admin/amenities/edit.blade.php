@extends('layouts.admin')
@section('title', $amenity->name)
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ $amenity->name }}</h1>
@include('admin.partials.translate-languages-form', ['type' => 'amenity', 'id' => $amenity->id])
<form method="post" action="{{ route('admin.amenities.update', $amenity) }}" class="mt-8 max-w-md space-y-4" data-admin-dirty-form>
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium">{{ __('Name') }} *</label>
        <input type="text" name="name" value="{{ old('name', $amenity->name) }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Slug *</label>
        <input type="text" name="slug" value="{{ old('slug', $amenity->slug) }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $amenity->sort_order) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <button type="submit" class="js-primary-save rounded-full bg-lake-900 px-6 py-2 text-sm text-white disabled:cursor-not-allowed disabled:opacity-50">{{ __('Save') }}</button>
</form>
<form method="post" action="{{ route('admin.amenities.destroy', $amenity) }}" class="mt-8" onsubmit="return confirm('Delete?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-sm text-red-600">{{ __('Delete') }}</button>
</form>
@endsection
