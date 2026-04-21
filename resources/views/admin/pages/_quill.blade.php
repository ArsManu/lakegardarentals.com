@props([
    'name',
    'label',
    'value' => '',
    'hint' => null,
    'minHeightClass' => 'min-h-[200px]',
    'required' => false,
])
<div class="space-y-1">
    <label class="block text-sm font-medium text-stone-700">{{ $label }}</label>
    @if($hint)
        <p class="text-xs text-stone-500">{{ $hint }}</p>
    @endif
    <div class="js-quill-field mt-1 overflow-hidden rounded-lg border border-stone-300 bg-white">
        <textarea
            name="{{ $name }}"
            class="hidden js-quill-source"
            @if($required) required @endif
        >{{ $value }}</textarea>
        <div class="js-quill-host {{ $minHeightClass }}"></div>
    </div>
</div>
