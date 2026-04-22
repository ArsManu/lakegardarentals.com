@props(['apartment'])

@php
    $img = $apartment->coverImagePath();
    $imgUrl = $img ? asset('storage/'.$img) : null;
    $shortPlain = \App\Support\PlainText::fromHtml($apartment->short_description);
@endphp

<article class="group flex flex-col overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm transition hover:shadow-md">
    <a href="{{ route('apartments.show', $apartment) }}" class="relative block aspect-[4/3] overflow-hidden bg-stone-200">
        @if($imgUrl)
            <img src="{{ $imgUrl }}" alt="{{ $apartment->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" loading="lazy">
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-stone-100 to-stone-300 text-center text-sm font-medium text-stone-500">
                {{ __('Photo coming soon') }}
            </div>
        @endif
    </a>
    <div class="flex flex-1 flex-col p-7">
        <h3 class="font-display text-2xl font-semibold leading-snug tracking-tight text-lake-950 sm:text-3xl">
            <a href="{{ route('apartments.show', $apartment) }}" class="hover:text-lake-800">{{ $apartment->name }}</a>
        </h3>
        <p class="mt-3 line-clamp-3 text-base leading-relaxed text-stone-600">{{ \Illuminate\Support\Str::limit($shortPlain, 280) }}</p>
        <ul class="mt-4 flex flex-wrap gap-2">
            @foreach($apartment->amenities->take(4) as $am)
                <li class="rounded-full bg-olive-100 px-3.5 py-1.5 text-sm font-medium text-olive-800">{{ $am->name }}</li>
            @endforeach
        </ul>
        <p class="mt-4 text-base text-stone-500">
            {{ $apartment->max_guests }} {{ __('guests') }} · {{ $apartment->bedrooms }} {{ __('bedrooms') }} · {{ $apartment->bathrooms }} {{ __('baths') }}
        </p>
        <p class="mt-3 font-display text-2xl font-semibold tracking-tight text-lake-900">{{ __('From') }} €{{ number_format($apartment->price_from, 0, ',', '.') }} <span class="text-base font-normal text-stone-500">{{ __('/ night') }}</span></p>
        <div class="mt-7 flex flex-wrap gap-3">
            <a href="{{ route('apartments.show', $apartment) }}#inquiry" class="inline-flex min-h-[3rem] flex-1 items-center justify-center rounded-full bg-lake-900 px-5 py-3 text-center text-base font-semibold text-white hover:bg-lake-800">{{ __('Request booking') }}</a>
            <a href="tel:{{ $sitePhoneTel }}" class="inline-flex min-h-[3rem] min-w-[6.75rem] items-center justify-center rounded-full border-2 border-lake-800 px-9 py-3 text-base font-semibold text-lake-900 sm:min-w-[7.5rem] sm:px-10">{{ __('Call') }}</a>
        </div>
    </div>
</article>
