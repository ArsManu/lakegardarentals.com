@props(['flex' => []])
@php
    $initial = old('blocks.flex_blocks', $flex);
    if (! is_array($initial)) {
        $initial = [];
    }
@endphp
<div
    data-flex-editor
    class="space-y-4 rounded-xl border border-stone-200 bg-stone-50/80 p-4"
    x-data='window.flexEditor(@json($initial))'
>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-sm font-medium text-stone-800">{{ __('Flexible sections') }}</p>
            <p class="text-xs text-stone-500">{{ __('Optional rows: image + text, full-width image, two images, or rich text. Upload images (stored on your server). Shown on the public page in order.') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <label class="sr-only" for="flex-add-type">{{ __('Add block') }}</label>
            <select
                id="flex-add-type"
                class="rounded-lg border-stone-300 text-sm"
                @change="if ($event.target.value) { add($event.target.value); $event.target.value = ''; }"
            >
                <option value="">{{ __('Add block…') }}</option>
                <option value="split_media">{{ __('Image + text (left/right)') }}</option>
                <option value="full_bleed_image">{{ __('Full-width image') }}</option>
                <option value="two_images">{{ __('Two images side by side') }}</option>
                <option value="rich_text">{{ __('Rich text (full width)') }}</option>
            </select>
        </div>
    </div>

    <template x-for="(block, index) in items" :key="block._id">
        <div class="rounded-lg border border-stone-200 bg-white p-4 shadow-sm">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2 border-b border-stone-100 pb-2">
                <span class="text-xs font-semibold uppercase tracking-wide text-stone-500" x-text="(block.type || '').replace(/_/g, ' ')"></span>
                <div class="flex flex-wrap gap-2">
                    <button type="button" class="rounded border border-stone-200 px-2 py-1 text-xs text-stone-600 hover:bg-stone-50" @click="moveUp(index)">{{ __('Up') }}</button>
                    <button type="button" class="rounded border border-stone-200 px-2 py-1 text-xs text-stone-600 hover:bg-stone-50" @click="moveDown(index)">{{ __('Down') }}</button>
                    <button type="button" class="rounded border border-red-200 px-2 py-1 text-xs text-red-700 hover:bg-red-50" @click="remove(index)">{{ __('Remove') }}</button>
                </div>
            </div>

            <input type="hidden" :name="'blocks[flex_blocks][' + index + '][type]'" :value="block.type">

            <div x-show="block.type === 'split_media'" class="space-y-3">
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Layout') }}</label>
                    <select class="mt-1 w-full rounded-lg border-stone-300 text-sm" x-model="block.layout" :name="'blocks[flex_blocks][' + index + '][layout]'">
                        <option value="image_left">{{ __('Image left, text right') }}</option>
                        <option value="image_right">{{ __('Image right, text left') }}</option>
                    </select>
                </div>
                <input type="hidden" :name="'blocks[flex_blocks][' + index + '][image_path]'" x-model="block.image_path">
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Image') }}</label>
                    <input type="file" data-flex-upload-kind="image" :data-flex-upload-index="index" :data-flex-upload-key="block._id" @change="setUploadName($event, 'image', index); block.image_preview_url = rememberFlexUpload($event, 'image', block._id)" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white">
                    <div x-show="block.image_preview_url || block.image_path" class="mt-2 flex items-center gap-2">
                        <img :src="block.image_preview_url || storageUrl(block.image_path)" alt="" class="h-14 w-24 rounded object-cover ring-1 ring-stone-200">
                        <span class="text-xs text-stone-500">{{ __('Current — upload a new file to replace.') }}</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Image alt text') }}</label>
                    <input type="text" class="mt-1 w-full rounded-lg border-stone-300 text-sm" x-model="block.image_alt" :name="'blocks[flex_blocks][' + index + '][image_alt]'">
                </div>
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Heading') }}</label>
                    <input type="text" class="mt-1 w-full rounded-lg border-stone-300 text-sm" x-model="block.heading" :name="'blocks[flex_blocks][' + index + '][heading]'">
                </div>
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Body') }}</label>
                    <div class="js-quill-field mt-1 overflow-hidden rounded-lg border border-stone-300 bg-white">
                        <textarea
                            class="hidden js-quill-source"
                            :name="'blocks[flex_blocks][' + index + '][body_html]'"
                            x-init="$el.value = block.body_html || ''; $nextTick(() => window.initAdminPageEditor.attachQuillToTextarea($el))"
                        ></textarea>
                        <div class="js-quill-host min-h-[140px]"></div>
                    </div>
                </div>
            </div>

            <div x-show="block.type === 'full_bleed_image'" class="space-y-3">
                <input type="hidden" :name="'blocks[flex_blocks][' + index + '][image_path]'" x-model="block.image_path">
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Image') }}</label>
                    <input type="file" data-flex-upload-kind="image" :data-flex-upload-index="index" :data-flex-upload-key="block._id" @change="setUploadName($event, 'image', index); block.image_preview_url = rememberFlexUpload($event, 'image', block._id)" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm text-stone-600 file:mr-3 file:rounded-lg file:border-0 file:bg-stone-800 file:px-3 file:py-1.5 file:text-xs file:font-medium file:text-white">
                    <div x-show="block.image_preview_url || block.image_path" class="mt-2 flex items-center gap-2">
                        <img :src="block.image_preview_url || storageUrl(block.image_path)" alt="" class="h-16 max-w-xs rounded object-cover ring-1 ring-stone-200">
                        <span class="text-xs text-stone-500">{{ __('Current — upload to replace.') }}</span>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Alt text') }}</label>
                    <input type="text" class="mt-1 w-full rounded-lg border-stone-300 text-sm" x-model="block.image_alt" :name="'blocks[flex_blocks][' + index + '][image_alt]'">
                </div>
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Caption (optional)') }}</label>
                    <input type="text" class="mt-1 w-full rounded-lg border-stone-300 text-sm" x-model="block.caption" :name="'blocks[flex_blocks][' + index + '][caption]'">
                </div>
            </div>

            <div x-show="block.type === 'two_images'" class="space-y-3">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Left image') }}</label>
                        <input type="hidden" :name="'blocks[flex_blocks][' + index + '][left_path]'" x-model="block.left_path">
                        <input type="file" data-flex-upload-kind="left" :data-flex-upload-index="index" :data-flex-upload-key="block._id" @change="setUploadName($event, 'left', index); block.left_preview_url = rememberFlexUpload($event, 'left', block._id)" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm text-stone-600 file:mr-2 file:rounded file:border-0 file:bg-stone-800 file:px-2 file:py-1 file:text-xs file:text-white">
                        <img x-show="block.left_preview_url || block.left_path" :src="block.left_preview_url || storageUrl(block.left_path)" alt="" class="mt-2 h-12 w-20 rounded object-cover ring-1 ring-stone-200">
                        <input type="text" class="mt-2 w-full rounded-lg border-stone-300 text-sm" placeholder="{{ __('Alt') }}" x-model="block.left_alt" :name="'blocks[flex_blocks][' + index + '][left_alt]'">
                    </div>
                    <div>
                        <label class="text-xs font-medium text-stone-600">{{ __('Right image') }}</label>
                        <input type="hidden" :name="'blocks[flex_blocks][' + index + '][right_path]'" x-model="block.right_path">
                        <input type="file" data-flex-upload-kind="right" :data-flex-upload-index="index" :data-flex-upload-key="block._id" @change="setUploadName($event, 'right', index); block.right_preview_url = rememberFlexUpload($event, 'right', block._id)" accept="image/jpeg,image/png,image/webp,image/gif" class="mt-1 block w-full text-sm text-stone-600 file:mr-2 file:rounded file:border-0 file:bg-stone-800 file:px-2 file:py-1 file:text-xs file:text-white">
                        <img x-show="block.right_preview_url || block.right_path" :src="block.right_preview_url || storageUrl(block.right_path)" alt="" class="mt-2 h-12 w-20 rounded object-cover ring-1 ring-stone-200">
                        <input type="text" class="mt-2 w-full rounded-lg border-stone-300 text-sm" placeholder="{{ __('Alt') }}" x-model="block.right_alt" :name="'blocks[flex_blocks][' + index + '][right_alt]'">
                    </div>
                </div>
            </div>

            <div x-show="block.type === 'rich_text'" class="space-y-3">
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Heading') }}</label>
                    <input type="text" class="mt-1 w-full rounded-lg border-stone-300 text-sm" x-model="block.heading" :name="'blocks[flex_blocks][' + index + '][heading]'" placeholder="{{ __('Optional — shown above the body') }}">
                </div>
                <div>
                    <label class="text-xs font-medium text-stone-600">{{ __('Content') }}</label>
                    <div class="js-quill-field mt-1 overflow-hidden rounded-lg border border-stone-300 bg-white">
                    <textarea
                        class="hidden js-quill-source"
                        :name="'blocks[flex_blocks][' + index + '][html]'"
                        x-init="$el.value = block.html || ''; $nextTick(() => window.initAdminPageEditor.attachQuillToTextarea($el))"
                    ></textarea>
                    <div class="js-quill-host min-h-[180px]"></div>
                </div>
                </div>
            </div>
        </div>
    </template>

    <p x-show="items.length === 0" class="text-sm text-stone-500">{{ __('No extra sections yet. Use “Add block” above.') }}</p>
</div>
