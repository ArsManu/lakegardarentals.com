@props(['apartment'])

@php
    $images = $apartment->relationLoaded('images') ? $apartment->images : $apartment->images()->orderBy('sort_order')->orderBy('id')->get();
    $galleryMaxMb = max(1, (int) round(config('lakegarda.admin_image_max_kb') / 1024));
@endphp

<div class="mt-12 max-w-5xl border-t border-stone-200 pt-10">
    <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Gallery images') }}</h2>
    <p class="mt-1 text-sm text-stone-500">{{ __('Upload as many photos as you need. Set sort priority (lower numbers first), use the arrows, edit alt text, replace a file, or remove a photo.') }}</p>

    <form method="post" action="{{ route('admin.apartments.images.store', $apartment) }}" enctype="multipart/form-data" class="mt-4 flex flex-wrap items-end gap-4">
        @csrf
        <div class="min-w-[200px] flex-1">
            <label class="text-xs font-medium text-stone-600">{{ __('Add images') }}</label>
            <input type="file" name="images[]" multiple required accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-2 file:text-sm file:font-medium file:text-white @error('images') border border-red-500 ring-1 ring-red-300 @enderror">
            @error('images')
                <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-xs text-stone-500">{{ __('JPEG, PNG, WebP or GIF — up to :max MB per file.', ['max' => $galleryMaxMb]) }}</p>
        </div>
        <button type="submit" class="rounded-full bg-stone-800 px-5 py-2 text-sm font-semibold text-white">{{ __('Upload') }}</button>
    </form>

    @if($images->isNotEmpty())
        @error('sort_order')
            <p class="mt-6 text-sm text-red-700">{{ $message }}</p>
        @enderror
        <ul class="mt-8 space-y-6">
            @foreach($images as $img)
                <li class="rounded-xl border border-stone-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start">
                        <a href="{{ asset('storage/'.$img->path) }}" target="_blank" rel="noopener" class="block flex-shrink-0 overflow-hidden rounded-lg ring-1 ring-stone-200">
                            <img src="{{ asset('storage/'.$img->path) }}" alt="{{ $img->alt_text }}" class="h-36 w-52 object-cover">
                        </a>
                        <div class="min-w-0 flex-1 space-y-3">
                            <form method="post" action="{{ route('admin.apartments.images.update', [$apartment, $img]) }}" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div class="flex flex-wrap gap-4">
                                    <div class="min-w-[8rem]">
                                        <label class="text-xs font-medium text-stone-600">{{ __('Sort priority') }}</label>
                                        <input type="number" name="sort_order" value="{{ old('sort_order', $img->sort_order) }}" min="0" max="65535" step="1" required class="mt-1 w-full rounded-lg border-stone-300 text-sm @error('sort_order') border-red-500 ring-1 ring-red-300 @enderror">
                                        <p class="mt-0.5 text-xs text-stone-500">{{ __('Lower = earlier in the gallery.') }}</p>
                                    </div>
                                    <div class="min-w-[12rem] flex-1">
                                        <label class="text-xs font-medium text-stone-600">{{ __('Alt text') }}</label>
                                        <input type="text" name="alt_text" value="{{ old('alt_text.'.$img->id, $img->alt_text) }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm">
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-medium text-stone-600">{{ __('Replace image') }}</label>
                                    <input type="file" name="replace_image" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm">
                                </div>
                                <button type="submit" class="rounded-full bg-lake-900 px-4 py-1.5 text-xs font-semibold text-white">{{ __('Save changes') }}</button>
                            </form>
                            <div class="flex flex-wrap gap-2 border-t border-stone-100 pt-3">
                                <form method="post" action="{{ route('admin.apartments.images.move-up', [$apartment, $img]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded border border-stone-200 px-2 py-1 text-xs text-stone-700 hover:bg-stone-50">{{ __('Move up') }}</button>
                                </form>
                                <form method="post" action="{{ route('admin.apartments.images.move-down', [$apartment, $img]) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded border border-stone-200 px-2 py-1 text-xs text-stone-700 hover:bg-stone-50">{{ __('Move down') }}</button>
                                </form>
                                <form method="post" action="{{ route('admin.apartments.images.destroy', [$apartment, $img]) }}" class="inline" data-confirm-question="{{ e(__('Remove this image?')) }}" onsubmit="return window.confirm(this.getAttribute('data-confirm-question') || '');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50">{{ __('Remove') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="mt-6 text-sm text-stone-500">{{ __('No gallery images yet. Use the field above to add photos.') }}</p>
    @endif
</div>
