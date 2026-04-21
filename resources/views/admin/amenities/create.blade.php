@extends('layouts.admin')
@section('title', __('New amenity'))
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ __('New amenity') }}</h1>
<form method="post" action="{{ route('admin.amenities.store') }}" class="mt-8 max-w-md space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium">{{ __('Name') }} *</label>
        <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Slug</label>
        <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <button type="submit" class="rounded-full bg-lake-900 px-6 py-2 text-sm text-white">{{ __('Create') }}</button>
</form>
@endsection
