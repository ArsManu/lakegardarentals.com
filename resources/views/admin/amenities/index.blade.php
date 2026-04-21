@extends('layouts.admin')
@section('title', __('Amenities'))
@section('content')
<div class="flex justify-between">
    <h1 class="font-display text-2xl font-semibold">{{ __('Amenities') }}</h1>
    <a href="{{ route('admin.amenities.create') }}" class="rounded-full bg-lake-900 px-4 py-2 text-sm text-white">{{ __('Add') }}</a>
</div>
<ul class="mt-8 divide-y divide-stone-200 rounded-xl border border-stone-200 bg-white">
    @foreach($amenities as $a)
        <li class="flex items-center justify-between px-4 py-3">
            <span>{{ $a->name }}</span>
            <a href="{{ route('admin.amenities.edit', $a) }}" class="text-lake-800">{{ __('Edit') }}</a>
        </li>
    @endforeach
</ul>
{{ $amenities->links() }}
@endsection
