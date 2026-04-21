@extends('layouts.admin')
@section('title', __('Testimonials'))
@section('content')
<div class="flex justify-between">
    <h1 class="font-display text-2xl font-semibold">{{ __('Testimonials') }}</h1>
    <a href="{{ route('admin.testimonials.create') }}" class="rounded-full bg-lake-900 px-4 py-2 text-sm text-white">{{ __('Add') }}</a>
</div>
<ul class="mt-8 space-y-3">
    @foreach($testimonials as $t)
        <li class="rounded-lg border border-stone-200 bg-white p-4">
            <p class="text-sm text-stone-700">{{ \Illuminate\Support\Str::limit(strip_tags($t->quote), 120) }}</p>
            <p class="mt-2 text-xs text-stone-500">{{ $t->author_name }} — <a href="{{ route('admin.testimonials.edit', $t) }}" class="text-lake-800">{{ __('Edit') }}</a></p>
        </li>
    @endforeach
</ul>
{{ $testimonials->links() }}
@endsection
