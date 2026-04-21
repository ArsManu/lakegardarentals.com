@php
    $b = $blocks ?? [];
    $why = old('blocks.why_points', $b['why_points'] ?? []);
    if (! is_array($why)) {
        $why = [];
    }
@endphp

<div class="space-y-8 rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
    <div>
        <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Home page content') }}</h2>
        <p class="mt-1 text-sm text-stone-500">{{ __('“Why us” cards, footer call-to-action, and flexible sections. Hero slides are edited separately.') }}</p>
    </div>

    <div class="flex flex-col gap-3 rounded-lg border border-lake-200 bg-gradient-to-br from-lake-50/80 to-white p-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-sm font-semibold text-lake-950">{{ __('Hero slideshow') }}</h3>
            <p class="mt-1 text-xs text-stone-600">{{ __('Up to five slides with images and CTAs — open the dedicated editor to avoid scrolling through the rest of the home content.') }}</p>
        </div>
        <a href="{{ route('admin.pages.hero-slideshow.edit', $page) }}" class="inline-flex shrink-0 items-center justify-center rounded-full bg-lake-900 px-5 py-2 text-sm font-semibold text-white hover:bg-lake-800">{{ __('Edit hero slideshow') }}</a>
    </div>

    <div>
        <h3 class="text-sm font-semibold text-stone-800">{{ __('“Why book with us” cards') }}</h3>
        <p class="text-xs text-stone-500">{{ __('Up to four cards. Leave a row empty to skip it.') }}</p>
        <div class="mt-3 space-y-4">
            @for($i = 0; $i < 4; $i++)
                @php
                    $pt = $why[$i] ?? ['title' => '', 'text' => ''];
                @endphp
                <div class="grid gap-3 rounded-lg border border-stone-100 bg-stone-50/50 p-3 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Title') }} {{ $i + 1 }}</label>
                        <input type="text" name="blocks[why_points][{{ $i }}][title]" value="{{ old('blocks.why_points.'.$i.'.title', $pt['title'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300 text-sm">
                    </div>
                    <div>
                        @include('admin.pages._quill', [
                            'name' => 'blocks[why_points]['.$i.'][text]',
                            'label' => __('Text').' '.($i + 1),
                            'value' => old('blocks.why_points.'.$i.'.text', $pt['text'] ?? ''),
                            'minHeightClass' => 'min-h-[100px]',
                        ])
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium text-stone-700">{{ __('Bottom CTA title') }}</label>
            <input type="text" name="blocks[cta_title]" value="{{ old('blocks.cta_title', $b['cta_title'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300">
        </div>
        <div>
            @include('admin.pages._quill', [
                'name' => 'blocks[cta_text]',
                'label' => __('Bottom CTA text'),
                'value' => old('blocks.cta_text', $b['cta_text'] ?? ''),
                'minHeightClass' => 'min-h-[100px]',
            ])
        </div>
    </div>

    @include('admin.pages._flex-blocks-editor', ['flex' => $b['flex_blocks'] ?? []])
</div>
