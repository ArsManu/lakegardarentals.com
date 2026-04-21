@extends('layouts.admin')
@section('title', __('New FAQ'))
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ __('New FAQ') }}</h1>
<form method="post" action="{{ route('admin.faqs.store') }}" class="mt-8 max-w-2xl space-y-4">
    @csrf
    <div>
        <label class="block text-sm font-medium">page_slug (home, lake-garda, contact)</label>
        <input type="text" name="page_slug" value="{{ old('page_slug') }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Question') }} *</label>
        <input type="text" name="question" value="{{ old('question') }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    @include('admin.pages._quill', [
        'name' => 'answer',
        'label' => __('Answer').' *',
        'value' => old('answer', ''),
        'minHeightClass' => 'min-h-[220px]',
        'required' => true,
    ])
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" id="is_active" checked class="rounded border-stone-300">
        <label for="is_active">{{ __('Active') }}</label>
    </div>
    <button type="submit" class="rounded-full bg-lake-900 px-6 py-2 text-sm text-white">{{ __('Create') }}</button>
</form>
@endsection
