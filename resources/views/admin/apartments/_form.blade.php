@php
    $a = $apartment;
@endphp
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">{{ __('Name') }} *</label>
        <input type="text" name="name" value="{{ old('name', $a->name ?? '') }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Slug</label>
        <input type="text" name="slug" value="{{ old('slug', $a->slug ?? '') }}" placeholder="auto from name" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
</div>
@include('admin.pages._quill', [
    'name' => 'short_description',
    'label' => __('Short description').' *',
    'value' => old('short_description', $a->short_description ?? ''),
    'hint' => __('Shown on listings and in search snippets. Bold and links are allowed.'),
    'minHeightClass' => 'min-h-[140px]',
    'required' => true,
])
@include('admin.pages._quill', [
    'name' => 'full_description',
    'label' => __('Full description').' *',
    'value' => old('full_description', $a->full_description ?? ''),
    'hint' => __('Main property copy on the apartment page.'),
    'minHeightClass' => 'min-h-[320px]',
    'required' => true,
])
<div>
    <label class="block text-sm font-medium">{{ __('Ideal for') }}</label>
    <input type="text" name="ideal_for" value="{{ old('ideal_for', $a->ideal_for ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
</div>
<div class="grid gap-5 sm:grid-cols-4">
    <div>
        <label class="block text-sm font-medium">{{ __('Max guests') }} *</label>
        <input type="number" name="max_guests" value="{{ old('max_guests', $a->max_guests ?? 2) }}" required min="1" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Bedrooms') }} *</label>
        <input type="number" name="bedrooms" value="{{ old('bedrooms', $a->bedrooms ?? 1) }}" required min="0" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Bathrooms') }} *</label>
        <input type="number" name="bathrooms" value="{{ old('bathrooms', $a->bathrooms ?? 1) }}" required min="0" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">m²</label>
        <input type="number" name="size_m2" value="{{ old('size_m2', $a->size_m2 ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">{{ __('Price from / night') }} *</label>
        <input type="number" step="0.01" name="price_from" value="{{ old('price_from', $a->price_from ?? '') }}" required class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('Sort order') }}</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $a->sort_order ?? 0) }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
</div>
<div>
    <label class="block text-sm font-medium">{{ __('Address') }}</label>
    <p class="mt-0.5 text-xs text-stone-500">{{ __('Full street address for Google Maps and the apartment page. Example: Via Roma 12, 37016 Garda VR, Italy') }}</p>
    <input type="text" name="address" value="{{ old('address', $a->address ?? '') }}" maxlength="512" autocomplete="street-address" class="mt-1 w-full rounded-lg border-stone-300">
</div>
@include('admin.pages._quill', [
    'name' => 'location_text',
    'label' => __('Location description'),
    'value' => old('location_text', $a->location_text ?? ''),
    'hint' => __('Optional: neighbourhood and surroundings (shown in the Location section below the address).'),
    'minHeightClass' => 'min-h-[160px]',
    'required' => false,
])
@include('admin.pages._quill', [
    'name' => 'check_in_out_note',
    'label' => __('Check-in / check-out note'),
    'value' => old('check_in_out_note', $a->check_in_out_note ?? ''),
    'minHeightClass' => 'min-h-[120px]',
    'required' => false,
])
@include('admin.pages._quill', [
    'name' => 'availability_note',
    'label' => __('Availability note'),
    'value' => old('availability_note', $a->availability_note ?? ''),
    'minHeightClass' => 'min-h-[120px]',
    'required' => false,
])
<div class="flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $a->is_active ?? true)) class="rounded border-stone-300">
    <label for="is_active">{{ __('Active') }}</label>
</div>
<div>
    <label class="block text-sm font-medium">{{ __('External listing URL') }}</label>
    <input type="url" name="external_listing_url" value="{{ old('external_listing_url', $a->external_listing_url ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">{{ __('Regional tourist rental code (CIR)') }}</label>
        <p class="mt-0.5 text-xs text-stone-500">{{ __('e.g. 023036-LOC-00059') }}</p>
        <input type="text" name="license_cir" value="{{ old('license_cir', $a?->license_cir ?? '') }}" maxlength="64" class="mt-1 w-full rounded-lg border-stone-300" autocomplete="off">
    </div>
    <div>
        <label class="block text-sm font-medium">{{ __('National listing code (CIN)') }}</label>
        <p class="mt-0.5 text-xs text-stone-500">{{ __('e.g. IT023036C2RGPU44NO') }}</p>
        <input type="text" name="license_cin" value="{{ old('license_cin', $a?->license_cin ?? '') }}" maxlength="64" class="mt-1 w-full rounded-lg border-stone-300" autocomplete="off">
    </div>
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">Meta title</label>
        <input type="text" name="meta_title" value="{{ old('meta_title', $a->meta_title ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">Canonical URL</label>
        <input type="url" name="canonical_url" value="{{ old('canonical_url', $a->canonical_url ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
</div>
<div>
    <label class="block text-sm font-medium">Meta description</label>
    <textarea name="meta_description" rows="2" class="mt-1 w-full rounded-lg border-stone-300">{{ old('meta_description', $a->meta_description ?? '') }}</textarea>
</div>
<div class="grid gap-5 sm:grid-cols-2">
    <div>
        <label class="block text-sm font-medium">OG title</label>
        <input type="text" name="og_title" value="{{ old('og_title', $a->og_title ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
    </div>
    <div>
        <label class="block text-sm font-medium">OG description</label>
        <textarea name="og_description" rows="2" class="mt-1 w-full rounded-lg border-stone-300">{{ old('og_description', $a->og_description ?? '') }}</textarea>
    </div>
</div>
<div>
    <label class="block text-sm font-medium">{{ __('Featured image') }}</label>
    <p class="text-xs text-stone-500">{{ __('Used on listings and as default if no gallery “cover” is set. Upload a file from your computer.') }}</p>
    @if($a && $a->featured_image)
        <div class="mt-2 flex flex-wrap items-end gap-4">
            <img src="{{ asset('storage/'.$a->featured_image) }}" alt="" class="h-36 max-w-md rounded-lg object-cover ring-1 ring-stone-200">
            <label class="flex items-center gap-2 text-sm text-stone-700">
                <input type="checkbox" name="remove_featured_image" value="1" @checked(old('remove_featured_image'))>
                {{ __('Remove featured image') }}
            </label>
        </div>
    @endif
    <input type="file" name="featured_image" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-2 w-full text-sm">
</div>
<div>
    <span class="block text-sm font-medium">{{ __('Amenities') }}</span>
    <div class="mt-2 grid gap-2 sm:grid-cols-2">
        @php $selectedAmenities = old('amenities', $a ? $a->amenities->pluck('id')->all() : []); @endphp
        @foreach($amenities as $am)
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="amenities[]" value="{{ $am->id }}" @checked(in_array($am->id, $selectedAmenities))>
                {{ $am->name }}
            </label>
        @endforeach
    </div>
</div>
