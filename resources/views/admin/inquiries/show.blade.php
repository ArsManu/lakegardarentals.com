@extends('layouts.admin')
@section('title', __('Inquiry #').$inquiry->id)
@section('content')
<a href="{{ route('admin.inquiries.index') }}" class="text-sm text-lake-800 hover:underline">← {{ __('Back') }}</a>
<h1 class="mt-4 font-display text-2xl font-semibold">{{ __('Inquiry') }} #{{ $inquiry->id }}</h1>
<dl class="mt-8 max-w-2xl space-y-3 text-sm">
    <div><dt class="font-medium text-stone-500">{{ __('Type') }}</dt><dd>{{ $inquiry->type }}</dd></div>
    <div><dt class="font-medium text-stone-500">{{ __('Status') }}</dt><dd>{{ $inquiry->status }}</dd></div>
    <div><dt class="font-medium text-stone-500">{{ __('Name') }}</dt><dd>{{ $inquiry->name }}</dd></div>
    <div><dt class="font-medium text-stone-500">{{ __('Email') }}</dt><dd><a href="mailto:{{ $inquiry->email }}" class="text-lake-800">{{ $inquiry->email }}</a></dd></div>
    <div><dt class="font-medium text-stone-500">{{ __('Phone') }}</dt><dd><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></dd></div>
    @if($inquiry->apartment)
        <div><dt class="font-medium text-stone-500">{{ __('Apartment') }}</dt><dd>{{ $inquiry->apartment->name }}</dd></div>
    @endif
    @if($inquiry->check_in)
        <div><dt class="font-medium text-stone-500">{{ __('Check-in') }}</dt><dd>{{ $inquiry->check_in->format('Y-m-d') }}</dd></div>
    @endif
    @if($inquiry->check_out)
        <div><dt class="font-medium text-stone-500">{{ __('Check-out') }}</dt><dd>{{ $inquiry->check_out->format('Y-m-d') }}</dd></div>
    @endif
    @if($inquiry->guests)
        <div><dt class="font-medium text-stone-500">{{ __('Guests') }}</dt><dd>{{ $inquiry->guests }}</dd></div>
    @endif
    @if($inquiry->message)
        <div><dt class="font-medium text-stone-500">{{ __('Message') }}</dt><dd class="whitespace-pre-wrap">{{ $inquiry->message }}</dd></div>
    @endif
    <div><dt class="font-medium text-stone-500">{{ __('Source') }}</dt><dd>{{ $inquiry->source_page }}</dd></div>
</dl>

<form method="post" action="{{ route('admin.inquiries.update', $inquiry) }}" class="mt-10 flex flex-wrap items-center gap-4">
    @csrf
    @method('PUT')
    <label class="text-sm font-medium">{{ __('Status') }}</label>
    <select name="status" class="rounded-lg border-stone-300">
        <option value="new" @selected($inquiry->status==='new')>new</option>
        <option value="contacted" @selected($inquiry->status==='contacted')>contacted</option>
        <option value="closed" @selected($inquiry->status==='closed')>closed</option>
    </select>
    <button type="submit" class="rounded-full bg-lake-900 px-4 py-2 text-sm text-white">{{ __('Update') }}</button>
</form>

<form method="post" action="{{ route('admin.inquiries.destroy', $inquiry) }}" class="mt-8" onsubmit="return confirm('Delete?');">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-sm text-red-600">{{ __('Delete') }}</button>
</form>
@endsection
