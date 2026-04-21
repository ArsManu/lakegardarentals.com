@php
    $b = $blocks ?? [];
@endphp

<div class="space-y-8 rounded-xl border border-stone-200 bg-white p-6 shadow-sm">
    <div>
        <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Contact page content') }}</h2>
        <p class="mt-1 text-sm text-stone-500">{{ __('Hero and reassurance text above the contact details and form.') }}</p>
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

    @include('admin.pages._quill', [
        'name' => 'blocks[reassurance]',
        'label' => __('Reassurance line'),
        'value' => old('blocks.reassurance', $b['reassurance'] ?? ''),
        'hint' => __('Short text next to “Get in touch”.'),
        'minHeightClass' => 'min-h-[100px]',
    ])

    <div class="rounded-lg border border-stone-200 bg-stone-50/80 p-4">
        <p class="text-sm font-medium text-stone-800">{{ __('Phone & email (site-wide)') }}</p>
        <p class="mt-1 text-xs text-stone-500">{{ __('Shown in the header, footer, and contact details. Leave blank to use the values from your environment config.') }}</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-stone-700">{{ __('Phone') }}</label>
                <input type="text" name="blocks[contact_phone]" value="{{ old('blocks.contact_phone', $b['contact_phone'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300" placeholder="{{ config('lakegarda.phone_display') }}" autocomplete="tel">
            </div>
            <div>
                <label class="block text-sm font-medium text-stone-700">{{ __('Email') }}</label>
                <input type="email" name="blocks[contact_email]" value="{{ old('blocks.contact_email', $b['contact_email'] ?? '') }}" class="mt-1 w-full rounded-lg border-stone-300" placeholder="{{ config('lakegarda.email') }}" autocomplete="email">
            </div>
        </div>
    </div>

    @include('admin.pages._flex-blocks-editor', ['flex' => $b['flex_blocks'] ?? []])
</div>
