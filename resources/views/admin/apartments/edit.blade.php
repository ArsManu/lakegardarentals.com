@extends('layouts.admin')

@section('title', $apartment->name)

@section('content')
<div class="flex flex-wrap items-center justify-between gap-4">
    <h1 class="font-display text-2xl font-semibold text-lake-950">{{ $apartment->name }}</h1>
    <div class="flex gap-4">
        <a href="{{ route('apartments.show', $apartment) }}" target="_blank" class="text-sm text-lake-800 hover:underline">{{ __('View public') }}</a>
        <form method="post" action="{{ route('admin.apartments.destroy', $apartment) }}" data-confirm-msg="{{ e(__('Delete this apartment?')) }}" onsubmit="return confirm(this.dataset.confirmMsg)">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-sm text-red-600 hover:underline">{{ __('Delete') }}</button>
        </form>
    </div>
</div>

<form method="post" action="{{ route('admin.apartments.update', $apartment) }}" enctype="multipart/form-data" class="mt-8 max-w-4xl space-y-5">
    @csrf
    @method('PUT')
    @include('admin.apartments._form', ['apartment' => $apartment, 'amenities' => $amenities])
    <button type="submit" class="rounded-full bg-lake-900 px-6 py-2 text-sm font-semibold text-white">{{ __('Save') }}</button>
</form>

@include('admin.apartments._gallery', ['apartment' => $apartment])

<div class="mt-12 max-w-4xl border-t border-stone-200 pt-10">
    <h2 class="font-display text-lg font-semibold">{{ __('Add season') }}</h2>
    <form method="post" action="{{ route('admin.apartments.seasons.store', $apartment) }}" class="mt-4 flex flex-wrap items-end gap-3">
        @csrf
        <input type="text" name="label" placeholder="{{ __('Label') }}" required class="rounded-lg border-stone-300">
        <input type="date" name="start_date" required class="rounded-lg border-stone-300">
        <input type="date" name="end_date" required class="rounded-lg border-stone-300">
        <input type="number" step="0.01" name="price_per_night" placeholder="€ / night" required class="w-32 rounded-lg border-stone-300">
        <button type="submit" class="rounded-full bg-lake-900 px-4 py-2 text-sm text-white">{{ __('Add') }}</button>
    </form>

    @foreach($apartment->seasons as $season)
        <div class="mt-6 rounded-lg border border-stone-200 bg-white p-4">
            <form method="post" action="{{ route('admin.apartments.seasons.update', [$apartment, $season]) }}" class="grid gap-3 sm:grid-cols-5 sm:items-end">
                @csrf
                @method('PUT')
                <div>
                    <label class="text-xs text-stone-500">{{ __('Label') }}</label>
                    <input type="text" name="label" value="{{ $season->label }}" class="mt-1 w-full rounded border-stone-300">
                </div>
                <div>
                    <label class="text-xs text-stone-500">{{ __('Start') }}</label>
                    <input type="date" name="start_date" value="{{ $season->start_date->format('Y-m-d') }}" class="mt-1 w-full rounded border-stone-300">
                </div>
                <div>
                    <label class="text-xs text-stone-500">{{ __('End') }}</label>
                    <input type="date" name="end_date" value="{{ $season->end_date->format('Y-m-d') }}" class="mt-1 w-full rounded border-stone-300">
                </div>
                <div>
                    <label class="text-xs text-stone-500">€ / {{ __('night') }}</label>
                    <input type="number" step="0.01" name="price_per_night" value="{{ $season->price_per_night }}" class="mt-1 w-full rounded border-stone-300">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="rounded-full bg-lake-900 px-4 py-2 text-sm text-white">{{ __('Save') }}</button>
                </div>
            </form>
            <form method="post" action="{{ route('admin.apartments.seasons.destroy', [$apartment, $season]) }}" class="mt-3 inline-block" onsubmit="return confirm('Delete season?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-red-600">{{ __('Delete season') }}</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
