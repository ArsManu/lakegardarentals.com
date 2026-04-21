@php
    $b = $blocks ?? [];
    $hs = old('blocks.hero_slides', $b['hero_slides'] ?? []);
    if (! is_array($hs)) {
        $hs = [];
    }
    $hs = array_values($hs);
    while (count($hs) < 5) {
        $hs[] = [
            'eyebrow' => '',
            'title' => '',
            'subtitle' => '',
            'image_path' => '',
            'image_alt' => '',
            'primary_cta_label' => '',
            'primary_cta_url' => '',
            'secondary_cta_label' => '',
            'secondary_cta_url' => '',
        ];
    }
    $hs = array_slice($hs, 0, 5);
@endphp

<div class="rounded-lg border border-lake-200 bg-lake-50/40 p-4">
    <h3 class="text-sm font-semibold text-lake-950">{{ __('Hero slideshow') }}</h3>
    <p class="mt-1 text-xs text-stone-600">{{ __('Add up to five slides with image, copy, and slide-specific CTA links. The homepage hero always uses this slideshow.') }}</p>
    <div class="mt-4 space-y-6">
        @for($i = 0; $i < 5; $i++)
            @php
                $slide = $hs[$i] ?? [
                    'eyebrow' => '',
                    'title' => '',
                    'subtitle' => '',
                    'image_path' => '',
                    'image_alt' => '',
                    'primary_cta_label' => '',
                    'primary_cta_url' => '',
                    'secondary_cta_label' => '',
                    'secondary_cta_url' => '',
                ];
                $path = $slide['image_path'] ?? '';
            @endphp
            <div
                class="rounded-lg border border-stone-200 bg-white p-4"
                x-data="{ previewUrl: '', savedUrl: @js(\App\Support\MediaUrl::public($path)) }"
            >
                <p class="text-xs font-semibold uppercase tracking-wide text-stone-400">{{ __('Slide') }} {{ $i + 1 }}</p>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Eyebrow') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][eyebrow]" value="{{ old('blocks.hero_slides.'.$i.'.eyebrow', $slide['eyebrow'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm" placeholder="{{ __('Garda · Lake Garda · Italy') }}">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Title') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][title]" value="{{ old('blocks.hero_slides.'.$i.'.title', $slide['title'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm">
                    </div>
                    <div class="sm:col-span-2">
                        @include('admin.pages._quill', [
                            'name' => 'blocks[hero_slides]['.$i.'][subtitle]',
                            'label' => __('Subtitle'),
                            'value' => old('blocks.hero_slides.'.$i.'.subtitle', $slide['subtitle'] ?? ''),
                            'minHeightClass' => 'min-h-[100px]',
                        ])
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Image alt text') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][image_alt]" value="{{ old('blocks.hero_slides.'.$i.'.image_alt', $slide['image_alt'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm">
                    </div>
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Primary CTA label') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][primary_cta_label]" value="{{ old('blocks.hero_slides.'.$i.'.primary_cta_label', $slide['primary_cta_label'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm" placeholder="{{ __('Call now') }}">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Primary CTA URL') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][primary_cta_url]" value="{{ old('blocks.hero_slides.'.$i.'.primary_cta_url', $slide['primary_cta_url'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm" placeholder="tel:+390000000000">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Secondary CTA label') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][secondary_cta_label]" value="{{ old('blocks.hero_slides.'.$i.'.secondary_cta_label', $slide['secondary_cta_label'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm" placeholder="{{ __('Request booking') }}">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Secondary CTA URL') }}</label>
                        <input type="text" name="blocks[hero_slides][{{ $i }}][secondary_cta_url]" value="{{ old('blocks.hero_slides.'.$i.'.secondary_cta_url', $slide['secondary_cta_url'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm" placeholder="/contact#inquiry">
                    </div>
                </div>
                <input type="hidden" name="blocks[hero_slides][{{ $i }}][image_path]" value="{{ old('blocks.hero_slides.'.$i.'.image_path', $path) }}">
                <div class="mt-3">
                    <label class="text-xs font-medium text-stone-600">{{ __('Image') }}</label>
                    <input
                        type="file"
                        name="hero_slide_image_file[{{ $i }}]"
                        accept="image/jpeg,image/png,image/webp,image/gif"
                        class="mt-1 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-lake-900 file:px-3 file:py-1.5 file:text-sm file:font-medium file:text-white"
                        @change="previewUrl = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : ''"
                    >
                    <p class="mt-1 text-xs text-stone-500">{{ __('JPG, PNG, WEBP, or GIF up to 20MB.') }}</p>
                    @error('hero_slide_image_file.'.$i)
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <div x-show="previewUrl || savedUrl" x-cloak class="mt-2 flex items-center gap-3">
                        <img :src="previewUrl || savedUrl" alt="" class="h-16 w-28 rounded-md object-cover ring-1 ring-stone-200">
                        <span class="text-xs text-stone-500" x-text="previewUrl ? '{{ __('Selected image preview — save to keep it.') }}' : '{{ __('Saved image — choose a file to replace.') }}'"></span>
                    </div>
                </div>
            </div>
        @endfor
    </div>
</div>
