@extends('layouts.admin')

@section('title', __('New apartment'))

@section('content')
<h1 class="font-display text-2xl font-semibold text-lake-950">{{ __('New apartment') }}</h1>

<form method="post" action="{{ route('admin.apartments.store') }}" enctype="multipart/form-data" class="mt-8 max-w-4xl space-y-5">
    @csrf
    @include('admin.apartments._form', ['apartment' => null, 'amenities' => $amenities])
    <button type="submit" class="rounded-full bg-lake-900 px-6 py-2 text-sm font-semibold text-white">{{ __('Create') }}</button>
</form>
@endsection
