@props([
    'actionUrl',
    'apartments' => collect(),
    'selectedApartmentId' => null,
    'sourcePage' => 'page',
    'submitLabel' => null,
])

@php
    $submitLabel = $submitLabel ?? __('Send request');
@endphp

<form action="{{ $actionUrl }}" method="post" class="space-y-6">
    @csrf
    <input type="hidden" name="source_page" value="{{ $sourcePage }}">

    {{-- Honeypot --}}
    <div class="hidden" aria-hidden="true">
        <label for="website">{{ __('Website') }}</label>
        <input type="text" name="website" id="website" tabindex="-1" autocomplete="off" value="">
    </div>

    {{-- Row 1: name, email, phone (3 cols from lg) --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label for="name" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Full name') }} *</label>
            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Email') }} *</label>
            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2 lg:col-span-1">
            <label for="phone" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Phone') }} *</label>
            <input type="tel" name="phone" id="phone" required value="{{ old('phone') }}"
                class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    @if($apartments->isNotEmpty())
    <div>
        <label for="apartment_id" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Preferred apartment') }}</label>
        <select name="apartment_id" id="apartment_id"
            class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            <option value="">{{ __('No preference') }}</option>
            @foreach($apartments as $apt)
                <option value="{{ $apt->id }}" @selected(old('apartment_id', $selectedApartmentId) == $apt->id)>{{ $apt->name }}</option>
            @endforeach
        </select>
        @error('apartment_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    @endif

    {{-- Row: check-in, check-out, guests --}}
    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <div>
            <label for="check_in" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Check-in') }} @if(str_contains($actionUrl, 'inquiry'))*@endif</label>
            <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}"
                @if(str_contains($actionUrl, 'inquiry')) required @endif
                class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            @error('check_in')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="check_out" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Check-out') }} @if(str_contains($actionUrl, 'inquiry'))*@endif</label>
            <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}"
                @if(str_contains($actionUrl, 'inquiry')) required @endif
                class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            @error('check_out')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="sm:col-span-2 lg:col-span-1">
            <label for="guests" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Guests') }} @if(str_contains($actionUrl, 'inquiry'))*@endif</label>
            <input type="number" name="guests" id="guests" min="1" max="30" value="{{ old('guests', 2) }}"
                @if(str_contains($actionUrl, 'inquiry')) required @endif
                class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">
            @error('guests')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="message" class="mb-1 block text-sm font-medium text-stone-700">{{ __('Message') }}</label>
        <textarea name="message" id="message" rows="4" class="w-full rounded-xl border-stone-300 shadow-sm focus:border-lake-700 focus:ring-lake-700">{{ old('message') }}</textarea>
        @error('message')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="flex flex-col gap-5 border-t border-stone-100 pt-6 sm:flex-row sm:items-start sm:justify-between sm:gap-8">
        <div class="flex min-w-0 flex-1 items-start gap-3">
            <input type="checkbox" name="consent" id="consent" value="1" required @checked(old('consent'))
                class="mt-1 rounded border-stone-300 text-lake-800 focus:ring-lake-700">
            <label for="consent" class="text-sm text-stone-600">{{ __('I agree to be contacted about my request and I have read the privacy notice.') }} *</label>
        </div>
        <button type="submit" class="w-full shrink-0 rounded-full bg-lake-900 px-8 py-3 text-center text-sm font-semibold text-white shadow-md transition hover:bg-lake-800 sm:w-auto sm:min-w-[12rem]">
            {{ $submitLabel }}
        </button>
    </div>
    @error('consent')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
</form>
