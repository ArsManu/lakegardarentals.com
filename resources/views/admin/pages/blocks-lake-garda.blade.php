@php
    $b = $blocks ?? [];
@endphp

<div class="space-y-8 rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
    <div>
        <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Lake Garda page content') }}</h2>
        <p class="mt-1 text-sm text-stone-500">{{ __('Hero image and title at the top, then build the rest of the page with flexible sections (text, image + text, galleries). The map and FAQ below still come from site settings and the FAQ admin.') }}</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-stone-700">{{ __('Hero title') }}</label>
            <input type="text" name="blocks[hero_title]" value="{{ old('blocks.hero_title', $b['hero_title'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
        <div>
            @include('admin.pages._quill', [
                'name' => 'blocks[hero_subtitle]',
                'label' => __('Hero subtitle'),
                'value' => old('blocks.hero_subtitle', $b['hero_subtitle'] ?? ''),
                'minHeightClass' => 'min-h-[120px]',
            ])
        </div>
    </div>

    <div class="rounded-lg border border-stone-200 bg-stone-50/50 p-4">
        @include('admin.pages._hero-header-image-field', ['blocks' => $b])
    </div>

    @include('admin.pages._flex-blocks-editor', ['flex' => $b['flex_blocks'] ?? []])
</div>
