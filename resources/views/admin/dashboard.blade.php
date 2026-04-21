@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
<h1 class="font-display text-2xl font-semibold text-lake-950">{{ __('Dashboard') }}</h1>
<div class="mt-8 grid gap-6 sm:grid-cols-3">
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <p class="text-sm text-stone-500">{{ __('Total inquiries') }}</p>
        <p class="mt-2 font-display text-3xl font-semibold text-lake-900">{{ $totalInquiries }}</p>
    </div>
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <p class="text-sm text-stone-500">{{ __('New inquiries') }}</p>
        <p class="mt-2 font-display text-3xl font-semibold text-amber-700">{{ $newInquiries }}</p>
    </div>
    <div class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <p class="text-sm text-stone-500">{{ __('Apartments') }}</p>
        <p class="mt-2 font-display text-3xl font-semibold text-lake-900">{{ $apartmentCount }}</p>
    </div>
</div>

<div class="mt-10">
    <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Recent inquiries') }}</h2>
    <div class="mt-4 overflow-x-auto rounded-xl border border-stone-200 bg-white">
        <table class="min-w-full text-left text-sm">
            <thead class="border-b border-stone-100 bg-stone-50">
                <tr>
                    <th class="px-4 py-3">{{ __('Date') }}</th>
                    <th class="px-4 py-3">{{ __('Name') }}</th>
                    <th class="px-4 py-3">{{ __('Type') }}</th>
                    <th class="px-4 py-3">{{ __('Status') }}</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent as $in)
                    <tr class="border-b border-stone-50">
                        <td class="px-4 py-3 text-stone-600">{{ $in->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3">{{ $in->name }}</td>
                        <td class="px-4 py-3">{{ $in->type }}</td>
                        <td class="px-4 py-3">{{ $in->status }}</td>
                        <td class="px-4 py-3"><a href="{{ route('admin.inquiries.show', $in) }}" class="text-lake-800 hover:underline">{{ __('Open') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-stone-500">{{ __('No inquiries yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
