@extends('layouts.admin')

@section('title', __('Apartments'))

@section('content')
<div class="flex items-center justify-between">
    <h1 class="font-display text-2xl font-semibold text-lake-950">{{ __('Apartments') }}</h1>
    <a href="{{ route('admin.apartments.create') }}" class="rounded-full bg-lake-900 px-4 py-2 text-sm font-semibold text-white">{{ __('Add apartment') }}</a>
</div>
@if(\App\Models\Page::query()->where('slug', 'apartments')->exists())
<div class="mt-6 rounded-xl border border-stone-200 bg-white p-5 shadow-sm">
    <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Apartments listing page') }}</h2>
    <p class="mt-1 max-w-2xl text-sm text-stone-600">{{ __('Edit the hero title, subtitle, and header image for the public /apartments page.') }}</p>
    <a href="{{ route('admin.pages.edit', ['page' => 'apartments']) }}" class="mt-4 inline-flex rounded-full border border-lake-200 bg-lake-50 px-4 py-2 text-sm font-semibold text-lake-900 hover:bg-lake-100">{{ __('Edit listing hero & SEO') }}</a>
</div>
@endif
<div class="mt-8 overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="border-b border-stone-100 bg-stone-50">
            <tr>
                <th class="px-4 py-3">{{ __('Name') }}</th>
                <th class="px-4 py-3">{{ __('Slug') }}</th>
                <th class="px-4 py-3">{{ __('Active') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($apartments as $a)
                <tr class="border-b border-stone-50">
                    <td class="px-4 py-3">{{ $a->name }}</td>
                    <td class="px-4 py-3 text-stone-500">{{ $a->slug }}</td>
                    <td class="px-4 py-3">{{ $a->is_active ? __('Yes') : __('No') }}</td>
                    <td class="px-4 py-3"><a href="{{ route('admin.apartments.edit', $a) }}" class="text-lake-800 hover:underline">{{ __('Edit') }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $apartments->links() }}
@endsection
