<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\AdminUploadedImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PageController extends Controller
{
    public function edit(Page $page): View
    {
        $this->authorize('update', $page);

        $blocks = old('blocks', $page->blocks ?? []);

        return view('admin.pages.edit', [
            'page' => $page,
            'blocks' => is_array($blocks) ? $blocks : [],
        ]);
    }

    public function editHeroSlideshow(Page $page): View
    {
        $this->authorize('update', $page);

        if ($page->slug !== 'home') {
            abort(404);
        }

        $blocks = old('blocks', $page->blocks ?? []);

        return view('admin.pages.hero-slideshow-edit', [
            'page' => $page,
            'blocks' => is_array($blocks) ? $blocks : [],
        ]);
    }

    public function updateHeroSlideshow(Request $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);

        if ($page->slug !== 'home') {
            abort(404);
        }

        $request->validate([
            'blocks' => ['nullable', 'array'],
            'blocks.hero_slides' => ['nullable', 'array'],
            'blocks.hero_slides.*.eyebrow' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.title' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.subtitle' => ['nullable', 'string', 'max:2000'],
            'blocks.hero_slides.*.image_path' => ['nullable', 'string', 'max:2048'],
            'blocks.hero_slides.*.image_alt' => ['nullable', 'string', 'max:500'],
            'blocks.hero_slides.*.primary_cta_label' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.primary_cta_url' => ['nullable', 'string', 'max:2048'],
            'blocks.hero_slides.*.secondary_cta_label' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.secondary_cta_url' => ['nullable', 'string', 'max:2048'],
            'hero_slide_image_file' => ['nullable', 'array'],
            'hero_slide_image_file.*' => ['nullable', 'image', $this->adminImageMaxKbRule()],
        ]);

        $existing = is_array($page->blocks) ? $page->blocks : [];
        $rawBlocks = $existing;
        $incoming = $request->input('blocks', []);
        if (! is_array($incoming)) {
            $incoming = [];
        }
        if (isset($incoming['hero_slides']) && is_array($incoming['hero_slides'])) {
            $rawBlocks['hero_slides'] = $incoming['hero_slides'];
        }

        $rawBlocks = $this->mergeHeroSlidesOnly($request, $page, $rawBlocks);
        $blocks = $this->sanitizeBlocksForPage('home', $rawBlocks);
        $page->update(['blocks' => $blocks]);

        return redirect()->route('admin.pages.hero-slideshow.edit', $page)->with('success', __('Saved.'));
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $this->authorize('update', $page);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'blocks' => ['nullable', 'array'],
            'blocks.hero_title' => ['nullable', 'string', 'max:255'],
            'blocks.hero_subtitle' => ['nullable', 'string', 'max:2000'],
            'blocks.hero_slides' => ['nullable', 'array'],
            'blocks.hero_slides.*.eyebrow' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.title' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.subtitle' => ['nullable', 'string', 'max:2000'],
            'blocks.hero_slides.*.image_path' => ['nullable', 'string', 'max:2048'],
            'blocks.hero_slides.*.image_alt' => ['nullable', 'string', 'max:500'],
            'blocks.hero_slides.*.primary_cta_label' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.primary_cta_url' => ['nullable', 'string', 'max:2048'],
            'blocks.hero_slides.*.secondary_cta_label' => ['nullable', 'string', 'max:255'],
            'blocks.hero_slides.*.secondary_cta_url' => ['nullable', 'string', 'max:2048'],
            'hero_slide_image_file' => ['nullable', 'array'],
            'hero_slide_image_file.*' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'blocks.cta_title' => ['nullable', 'string', 'max:255'],
            'blocks.cta_text' => ['nullable', 'string', 'max:2000'],
            'blocks.why_points' => ['nullable', 'array', 'max:12'],
            'blocks.why_points.*.title' => ['nullable', 'string', 'max:255'],
            'blocks.why_points.*.text' => ['nullable', 'string', 'max:2000'],
            'blocks.reassurance' => ['nullable', 'string', 'max:2000'],
            'blocks.contact_phone' => ['nullable', 'string', 'max:100'],
            'blocks.contact_email' => ['nullable', 'string', 'email', 'max:255'],
            'blocks.flex_blocks' => ['nullable', 'array'],
            'blocks.flex_blocks.*.type' => ['required', 'string', 'in:split_media,full_bleed_image,two_images,rich_text'],
            'blocks.flex_blocks.*.layout' => ['nullable', 'string', 'in:image_left,image_right'],
            'blocks.flex_blocks.*.image_path' => ['nullable', 'string', 'max:2048'],
            'blocks.flex_blocks.*.image_url' => ['nullable', 'string', 'max:2048'],
            'blocks.flex_blocks.*.image_file' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'flex_block_image_file' => ['nullable', 'array'],
            'flex_block_image_file.*' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'blocks.flex_blocks.*.image_alt' => ['nullable', 'string', 'max:500'],
            'blocks.flex_blocks.*.heading' => ['nullable', 'string', 'max:255'],
            'blocks.flex_blocks.*.body_html' => ['nullable', 'string', 'max:100000'],
            'blocks.flex_blocks.*.caption' => ['nullable', 'string', 'max:500'],
            'blocks.flex_blocks.*.left_path' => ['nullable', 'string', 'max:2048'],
            'blocks.flex_blocks.*.left_url' => ['nullable', 'string', 'max:2048'],
            'blocks.flex_blocks.*.left_alt' => ['nullable', 'string', 'max:500'],
            'blocks.flex_blocks.*.left_file' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'flex_block_left_file' => ['nullable', 'array'],
            'flex_block_left_file.*' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'blocks.flex_blocks.*.right_path' => ['nullable', 'string', 'max:2048'],
            'blocks.flex_blocks.*.right_url' => ['nullable', 'string', 'max:2048'],
            'blocks.flex_blocks.*.right_alt' => ['nullable', 'string', 'max:500'],
            'blocks.flex_blocks.*.right_file' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'flex_block_right_file' => ['nullable', 'array'],
            'flex_block_right_file.*' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'blocks.flex_blocks.*.html' => ['nullable', 'string', 'max:100000'],
            'blocks.flex_blocks.*.heading' => ['nullable', 'string', 'max:255'],
            'blocks.hero_header_image_path' => ['nullable', 'string', 'max:2048'],
            'hero_header_image_file' => ['nullable', 'image', $this->adminImageMaxKbRule()],
            'remove_hero_header_image' => ['sometimes', 'boolean'],
        ]);

        // Use full request input so nested blocks (hero_slides, etc.) are not dropped; validated()
        // does not reliably preserve deeply nested arrays the same as the raw request.
        $rawBlocks = $request->input('blocks', []);
        if (! is_array($rawBlocks)) {
            $rawBlocks = [];
        }
        if ($page->slug === 'home' && ! array_key_exists('hero_slides', $rawBlocks)) {
            $existingBlocks = is_array($page->blocks) ? $page->blocks : [];
            $rawBlocks['hero_slides'] = $existingBlocks['hero_slides'] ?? [];
        }
        unset($data['blocks']);

        $rawBlocks = $this->mergeUploadedImages($request, $page, $rawBlocks);

        $blocks = $this->sanitizeBlocksForPage($page->slug, $rawBlocks);

        $page->update(array_merge($data, ['blocks' => $blocks]));

        return redirect()->route('admin.pages.edit', $page)->with('success', __('Saved.'));
    }

    /**
     * @param  array<string, mixed>  $rawBlocks
     * @return array<string, mixed>
     */
    private function mergeUploadedImages(Request $request, Page $page, array $rawBlocks): array
    {
        if ($page->slug === 'home' && isset($rawBlocks['hero_slides']) && is_array($rawBlocks['hero_slides'])) {
            $rawBlocks['hero_slides'] = $this->mergeHeroSlideFiles($request, $page, $rawBlocks['hero_slides']);
        }

        if (isset($rawBlocks['flex_blocks']) && is_array($rawBlocks['flex_blocks'])) {
            $rawBlocks['flex_blocks'] = $this->mergeFlexBlockFiles($request, $page, $rawBlocks['flex_blocks']);
        }

        if (in_array($page->slug, ['lake-garda', 'contact', 'apartments'], true)) {
            $rawBlocks = $this->mergeInnerPageHeaderImage($request, $page, $rawBlocks);
        }

        return $rawBlocks;
    }

    /**
     * @param  array<string, mixed>  $rawBlocks
     * @return array<string, mixed>
     */
    private function mergeHeroSlidesOnly(Request $request, Page $page, array $rawBlocks): array
    {
        if (isset($rawBlocks['hero_slides']) && is_array($rawBlocks['hero_slides'])) {
            $rawBlocks['hero_slides'] = $this->mergeHeroSlideFiles($request, $page, $rawBlocks['hero_slides']);
        }

        return $rawBlocks;
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function mergeInnerPageHeaderImage(Request $request, Page $page, array $raw): array
    {
        $base = 'pages/'.$page->id.'/header';
        $path = $this->str($raw['hero_header_image_path'] ?? null, 2048);
        if ($request->boolean('remove_hero_header_image')) {
            $this->deleteStoredPublicPath($path);
            $path = '';
        } elseif ($request->hasFile('hero_header_image_file')) {
            $this->deleteStoredPublicPath($path);
            try {
                $path = AdminUploadedImage::storeAsWebp($request->file('hero_header_image_file'), $base, 'public');
            } catch (Throwable $e) {
                report($e);
                throw ValidationException::withMessages([
                    'hero_header_image_file' => __('Image could not be processed. Use JPEG or PNG.'),
                ]);
            }
        }
        $raw['hero_header_image_path'] = $path;

        return $raw;
    }

    /**
     * @param  array<int|string, mixed>  $slides
     * @return array<int, mixed>
     */
    private function mergeHeroSlideFiles(Request $request, Page $page, array $slides): array
    {
        $base = 'pages/'.$page->id.'/hero';
        $uploads = $request->file('hero_slide_image_file', []);
        if (! is_array($uploads)) {
            $uploads = [];
        }

        $out = [];
        foreach ($slides as $i => $slide) {
            if (! is_array($slide)) {
                continue;
            }
            $path = $this->str($slide['image_path'] ?? null, 2048);
            $file = $uploads[$i] ?? null;
            if ($file instanceof \Illuminate\Http\UploadedFile && $file->isValid()) {
                $this->deleteStoredPublicPath($path);
                try {
                    $path = AdminUploadedImage::storeAsWebp($file, $base, 'public');
                } catch (Throwable $e) {
                    report($e);
                    throw ValidationException::withMessages([
                        'hero_slide_image_file.'.$i => __('Image could not be processed. Use JPEG or PNG.'),
                    ]);
                }
            }
            $slide['image_path'] = $path;
            unset($slide['image_file']);
            $out[] = $slide;
        }

        return $out;
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, mixed>
     */
    private function mergeFlexBlockFiles(Request $request, Page $page, array $items): array
    {
        $base = 'pages/'.$page->id.'/flex';
        $singleUploads = $request->file('flex_block_image_file', []);
        $leftUploads = $request->file('flex_block_left_file', []);
        $rightUploads = $request->file('flex_block_right_file', []);
        if (! is_array($singleUploads)) {
            $singleUploads = [];
        }
        if (! is_array($leftUploads)) {
            $leftUploads = [];
        }
        if (! is_array($rightUploads)) {
            $rightUploads = [];
        }
        $uploadedFlex = $request->file('blocks.flex_blocks', []);
        if (! is_array($uploadedFlex)) {
            $uploadedFlex = [];
        }

        foreach ($items as $i => &$block) {
            if (! is_array($block)) {
                continue;
            }
            $type = $block['type'] ?? '';
            if ($type === 'split_media' || $type === 'full_bleed_image') {
                $path = $this->str($block['image_path'] ?? $block['image_url'] ?? null, 2048);
                $file = $this->extractUploadedFile($singleUploads[$i] ?? ($uploadedFlex[$i]['image_file'] ?? null));
                if ($file !== null) {
                    $this->deleteStoredPublicPath($path);
                    try {
                        $path = AdminUploadedImage::storeAsWebp($file, $base, 'public');
                    } catch (Throwable $e) {
                        report($e);
                        throw ValidationException::withMessages([
                            'blocks.flex_blocks.'.$i.'.image_file' => __('Image could not be processed. Use JPEG or PNG.'),
                        ]);
                    }
                }
                $block['image_path'] = $path;
                unset($block['image_url'], $block['image_file']);
            }
            if ($type === 'two_images') {
                foreach (['left', 'right'] as $side) {
                    $keyPath = $side.'_path';
                    $keyUrl = $side.'_url';
                    $path = $this->str($block[$keyPath] ?? $block[$keyUrl] ?? null, 2048);
                    $sideUploads = $side === 'left' ? $leftUploads : $rightUploads;
                    $file = $this->extractUploadedFile($sideUploads[$i] ?? ($uploadedFlex[$i][$side.'_file'] ?? null));
                    if ($file !== null) {
                        $this->deleteStoredPublicPath($path);
                        try {
                            $path = AdminUploadedImage::storeAsWebp($file, $base, 'public');
                        } catch (Throwable $e) {
                            report($e);
                            throw ValidationException::withMessages([
                                'blocks.flex_blocks.'.$i.'.'.$side.'_file' => __('Image could not be processed. Use JPEG or PNG.'),
                            ]);
                        }
                    }
                    $block[$keyPath] = $path;
                    unset($block[$keyUrl], $block[$side.'_file']);
                }
            }
        }
        unset($block);

        return $items;
    }

    private function extractUploadedFile(mixed $value): ?UploadedFile
    {
        if ($value instanceof UploadedFile) {
            return $value->isValid() ? $value : null;
        }
        if (is_array($value)) {
            foreach ($value as $nested) {
                $file = $this->extractUploadedFile($nested);
                if ($file !== null) {
                    return $file;
                }
            }
        }

        return null;
    }

    private function deleteStoredPublicPath(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return;
        }
        Storage::disk('public')->delete($path);
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array<string, mixed>
     */
    private function sanitizeBlocksForPage(string $slug, array $raw): array
    {
        $flex = $this->sanitizeFlexBlocks($raw['flex_blocks'] ?? []);

        return match ($slug) {
            'home' => [
                'hero_slides' => $this->sanitizeHeroSlides($raw['hero_slides'] ?? []),
                'why_points' => $this->sanitizeWhyPoints($raw['why_points'] ?? []),
                'cta_title' => $this->str($raw['cta_title'] ?? null, 255),
                'cta_text' => $this->str($raw['cta_text'] ?? null, 2000),
                'flex_blocks' => $flex,
            ],
            'lake-garda' => [
                'hero_title' => $this->str($raw['hero_title'] ?? null, 255),
                'hero_subtitle' => $this->str($raw['hero_subtitle'] ?? null, 2000),
                'hero_header_image_path' => $this->str($raw['hero_header_image_path'] ?? null, 2048),
                'flex_blocks' => $flex,
            ],
            'contact' => [
                'hero_title' => $this->str($raw['hero_title'] ?? null, 255),
                'hero_subtitle' => $this->str($raw['hero_subtitle'] ?? null, 2000),
                'hero_header_image_path' => $this->str($raw['hero_header_image_path'] ?? null, 2048),
                'reassurance' => $this->str($raw['reassurance'] ?? null, 2000),
                'contact_phone' => $this->str($raw['contact_phone'] ?? null, 100),
                'contact_email' => $this->str($raw['contact_email'] ?? null, 255),
                'flex_blocks' => $flex,
            ],
            'apartments' => [
                'hero_title' => $this->str($raw['hero_title'] ?? null, 255),
                'hero_subtitle' => $this->str($raw['hero_subtitle'] ?? null, 2000),
                'hero_header_image_path' => $this->str($raw['hero_header_image_path'] ?? null, 2048),
                'flex_blocks' => $flex,
            ],
            default => [
                'flex_blocks' => $flex,
            ],
        };
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{eyebrow: string, title: string, subtitle: string, image_path: string, image_alt: string, primary_cta_label: string, primary_cta_url: string, secondary_cta_label: string, secondary_cta_url: string}>
     */
    private function sanitizeHeroSlides(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $path = $this->str($row['image_path'] ?? null, 2048);
            if ($path === '') {
                continue;
            }
            $out[] = [
                'eyebrow' => $this->str($row['eyebrow'] ?? null, 255),
                'title' => $this->str($row['title'] ?? null, 255),
                'subtitle' => $this->str($row['subtitle'] ?? null, 2000),
                'image_path' => $path,
                'image_alt' => $this->str($row['image_alt'] ?? null, 500),
                'primary_cta_label' => $this->str($row['primary_cta_label'] ?? null, 255),
                'primary_cta_url' => $this->str($row['primary_cta_url'] ?? null, 2048),
                'secondary_cta_label' => $this->str($row['secondary_cta_label'] ?? null, 255),
                'secondary_cta_url' => $this->str($row['secondary_cta_url'] ?? null, 2048),
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, mixed>  $rows
     * @return array<int, array{title: string, text: string}>
     */
    private function sanitizeWhyPoints(array $rows): array
    {
        $out = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));
            if ($title === '' && $text === '') {
                continue;
            }
            $out[] = [
                'title' => $this->str($title, 255),
                'text' => $this->str($text, 2000),
            ];
        }

        return $out;
    }

    /**
     * @param  array<int, mixed>  $items
     * @return array<int, array<string, mixed>>
     */
    private function sanitizeFlexBlocks(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            unset($item['_id']);
            $type = $item['type'] ?? '';
            switch ($type) {
                case 'split_media':
                    $layout = ($item['layout'] ?? '') === 'image_right' ? 'image_right' : 'image_left';
                    $img = $this->str($item['image_path'] ?? $item['image_url'] ?? null, 2048);
                    $out[] = [
                        'type' => 'split_media',
                        'layout' => $layout,
                        'image_path' => $img,
                        'image_alt' => $this->str($item['image_alt'] ?? null, 500),
                        'heading' => $this->str($item['heading'] ?? null, 255),
                        'body_html' => $this->html($item['body_html'] ?? ''),
                    ];
                    break;
                case 'full_bleed_image':
                    $img = $this->str($item['image_path'] ?? $item['image_url'] ?? null, 2048);
                    $out[] = [
                        'type' => 'full_bleed_image',
                        'image_path' => $img,
                        'image_alt' => $this->str($item['image_alt'] ?? null, 500),
                        'caption' => $this->str($item['caption'] ?? null, 500),
                    ];
                    break;
                case 'two_images':
                    $out[] = [
                        'type' => 'two_images',
                        'left_path' => $this->str($item['left_path'] ?? $item['left_url'] ?? null, 2048),
                        'left_alt' => $this->str($item['left_alt'] ?? null, 500),
                        'right_path' => $this->str($item['right_path'] ?? $item['right_url'] ?? null, 2048),
                        'right_alt' => $this->str($item['right_alt'] ?? null, 500),
                    ];
                    break;
                case 'rich_text':
                    $out[] = [
                        'type' => 'rich_text',
                        'heading' => $this->str($item['heading'] ?? null, 255),
                        'html' => $this->html($item['html'] ?? ''),
                    ];
                    break;
            }
        }

        return $out;
    }

    private function adminImageMaxKbRule(): string
    {
        return 'max:'.config('lakegarda.admin_image_max_kb');
    }

    private function str(?string $value, int $max): string
    {
        $value = trim((string) $value);

        return mb_substr($value, 0, $max);
    }

    private function html(string $value): string
    {
        $value = str_replace(["\u{00A0}", '&nbsp;', '&#160;'], ' ', $value);
        $allowed = '<p><br><a><strong><b><em><i><u><s><strike><del><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><code><pre><span><div><sup><sub>';

        return strip_tags($value, $allowed);
    }
}
