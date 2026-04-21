@php
    $b = $blocks ?? [];
    $hh = old('blocks.hero_header_image_path', $b['hero_header_image_path'] ?? '');
@endphp
<input type="hidden" name="blocks[hero_header_image_path]" value="{{ $hh }}">
<div x-data="{ previewUrl: '', savedUrl: @js(\App\Support\MediaUrl::public($hh)) }">
    <label class="block text-sm font-medium text-stone-700">{{ __('Page header background image') }}</label>
    <p class="mt-1 text-xs text-stone-500">{{ __('Optional. Displayed behind the page title and breadcrumb. Saved as WebP, max width 2500px.') }}</p>
    <input
        type="file"
        name="hero_header_image_file"
        accept="image/jpeg,image/png,image/webp,image/gif"
        class="mt-2 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white"
        @change="previewUrl = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : ''"
    >
    <p class="mt-1 text-xs text-stone-500">{{ __('JPG, PNG, WEBP, or GIF up to 20MB.') }}</p>
    @error('hero_header_image_file')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
    <div class="mt-3 flex flex-wrap items-center gap-3" x-show="previewUrl || savedUrl" x-cloak>
        <img :src="previewUrl || savedUrl" alt="" class="h-28 w-full max-w-md rounded-md object-cover object-center ring-1 ring-stone-200">
        <span class="text-xs text-stone-500" x-text="previewUrl ? '{{ __('Selected image preview — save to keep it.') }}' : '{{ __('Saved image.') }}'"></span>
        <label class="flex items-center gap-2 text-sm text-stone-600" x-show="savedUrl">
            <input type="checkbox" name="remove_hero_header_image" value="1" @checked(old('remove_hero_header_image'))>
            {{ __('Remove image') }}
        </label>
    </div>
</div>
