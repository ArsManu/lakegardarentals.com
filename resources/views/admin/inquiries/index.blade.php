@extends('layouts.admin')
@section('title', __('Inquiries'))
@section('content')
<h1 class="font-display text-2xl font-semibold">{{ __('Inquiries') }}</h1>
<form method="get" class="mt-6 flex flex-wrap gap-4 text-sm">
    <select name="status" class="rounded-lg border-stone-300">
        <option value="">{{ __('All statuses') }}</option>
        <option value="new" @selected(request('status')==='new')>new</option>
        <option value="contacted" @selected(request('status')==='contacted')>contacted</option>
        <option value="closed" @selected(request('status')==='closed')>closed</option>
    </select>
    <select name="type" class="rounded-lg border-stone-300">
        <option value="">{{ __('All types') }}</option>
        <option value="booking" @selected(request('type')==='booking')>booking</option>
        <option value="contact" @selected(request('type')==='contact')>contact</option>
    </select>
    <button type="submit" class="rounded-full bg-stone-200 px-4 py-1">{{ __('Filter') }}</button>
</form>
<div class="mt-8 overflow-x-auto rounded-xl border border-stone-200 bg-white">
    <table class="min-w-full text-left text-sm">
        <thead class="bg-stone-50">
            <tr>
                <th class="px-4 py-3">{{ __('Date') }}</th>
                <th class="px-4 py-3">{{ __('Name') }}</th>
                <th class="px-4 py-3">{{ __('Email') }}</th>
                <th class="px-4 py-3">{{ __('Type') }}</th>
                <th class="px-4 py-3">{{ __('Status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($inquiries as $in)
                <tr class="border-t border-stone-100">
                    <td class="px-4 py-3">{{ $in->created_at->format('Y-m-d H:i') }}</td>
                    <td class="px-4 py-3">{{ $in->name }}</td>
                    <td class="px-4 py-3">{{ $in->email }}</td>
                    <td class="px-4 py-3">{{ $in->type }}</td>
                    <td class="px-4 py-3">{{ $in->status }}</td>
                    <td class="px-4 py-3"><a href="{{ route('admin.inquiries.show', $in) }}" class="text-lake-800">{{ __('Open') }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
{{ $inquiries->links() }}
@endsection
