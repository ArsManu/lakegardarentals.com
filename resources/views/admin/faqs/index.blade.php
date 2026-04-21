@extends('layouts.admin')
@section('title', __('FAQs'))
@section('content')
<div class="flex justify-between">
    <h1 class="font-display text-2xl font-semibold">{{ __('FAQs') }}</h1>
    <a href="{{ route('admin.faqs.create') }}" class="rounded-full bg-lake-900 px-4 py-2 text-sm text-white">{{ __('Add') }}</a>
</div>
<ul class="mt-8 space-y-2">
    @foreach($faqs as $f)
        <li class="flex items-center justify-between rounded-lg border border-stone-200 bg-white px-4 py-3 text-sm">
            <span>{{ $f->question }} <span class="text-stone-400">({{ $f->page_slug ?? '—' }})</span></span>
            <a href="{{ route('admin.faqs.edit', $f) }}" class="text-lake-800">{{ __('Edit') }}</a>
        </li>
    @endforeach
</ul>
{{ $faqs->links() }}
@endsection
