@extends('layouts.admin')
@section('title', __('Edit FAQ'))
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ __('Edit FAQ') }}</h1>
<form method="post" action="{{ route('admin.faqs.update', $faq) }}" class="mt-8 max-w-2xl space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label class="block text-sm font-medium">page_slug</label>
        <input type="text" name="page_slug" value="{{ old('page_slug', $faq->page_slug) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Question') }} *</label>
        <input type="text" name="question" value="{{ old('question', $faq->question) }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    @include('admin.pages._quill', [
        'name' => 'answer',
        'label' => __('Answer').' *',
        'value' => old('answer', $faq->answer),
        'minHeightClass' => 'min-h-[220px]',
        'required' => true,
    ])
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $faq->is_active)) class="rounded border-stone-300">
        <label for="is_active">{{ __('Active') }}</label>
    </div>
    <button type="submit" class="rounded-full bg-lake-900 px-6 py-2 text-sm text-white">{{ __('Save') }}</button>
</form>
<form method="post" action="{{ route('admin.faqs.destroy', $faq) }}" class="mt-8" onsubmit="return confirm('Delete?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-sm text-red-600">{{ __('Delete') }}</button>
</form>
@endsection
