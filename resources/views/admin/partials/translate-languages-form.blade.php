<div class="mb-6 max-w-4xl rounded-lg border border-stone-200 bg-white p-4 shadow-sm">
    <p class="text-sm text-stone-600">{{ __('Regenerate stored German and Italian from the current English text.') }}</p>
    <form method="post" action="{{ route('admin.translate') }}" class="mt-3">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="id" value="{{ $id }}">
        <button type="submit" class="inline-flex items-center rounded-full border border-lake-800 bg-white px-5 py-2 text-sm font-semibold text-lake-900 hover:bg-stone-50">
            {{ __('Translate to other languages') }}
        </button>
    </form>
</div>
