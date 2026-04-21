<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentImage;
use App\Support\AdminUploadedImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ApartmentImageController extends Controller
{
    private function imageMaxRule(): string
    {
        return 'max:'.config('lakegarda.admin_image_max_kb');
    }

    public function store(Request $request, Apartment $apartment): RedirectResponse
    {
        $this->authorize('update', $apartment);

        // Large / multiple files: decoding happens before AdminUploadedImage runs; keep headroom for the whole request.
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', '180');

        // Browsers may send a single file as one UploadedFile; validation expects `images` to be an array.
        $raw = $request->file('images');
        $files = match (true) {
            $raw instanceof UploadedFile => [$raw],
            is_array($raw) => array_values(array_filter(
                $raw,
                fn ($f) => $f instanceof UploadedFile
            )),
            default => [],
        };

        // If nothing under `images`, collect any images.* keys (edge-case request shapes).
        if ($files === []) {
            foreach ($request->allFiles() as $key => $fileOrList) {
                if (! is_string($key) || ! str_starts_with($key, 'images')) {
                    continue;
                }
                if ($fileOrList instanceof UploadedFile) {
                    $files[] = $fileOrList;
                } elseif (is_array($fileOrList)) {
                    foreach ($fileOrList as $f) {
                        if ($f instanceof UploadedFile) {
                            $files[] = $f;
                        }
                    }
                }
            }
            $files = array_values($files);
        }

        if ($files === []) {
            return back()->withErrors(['images' => __('Choose one or more images to upload.')]);
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile && ! $file->isValid()) {
                return back()->withErrors([
                    'images' => __('Upload failed (:message). Check PHP upload_max_filesize and post_max_size.', [
                        'message' => $file->getErrorMessage(),
                    ]),
                ]);
            }

            // Use attribute name `images` so errors show beside the gallery field (not `upload`).
            Validator::make(
                ['images' => $file],
                ['images' => ['required', 'image', $this->imageMaxRule()]]
            )->validate();
        }

        $maxSort = (int) $apartment->images()->max('sort_order');

        foreach ($files as $file) {
            try {
                $path = AdminUploadedImage::storeAsWebp($file, "apartments/{$apartment->id}/gallery", 'public');
            } catch (Throwable $e) {
                report($e);

                return back()->withErrors(['images' => __('One or more images could not be processed. Use JPEG or PNG.')]);
            }
            $maxSort++;
            ApartmentImage::query()->create([
                'apartment_id' => $apartment->id,
                'path' => $path,
                'alt_text' => $apartment->name,
                'sort_order' => $maxSort,
                'is_cover' => false,
            ]);
        }

        return back()->with('success', __('Images uploaded.'));
    }

    public function update(Request $request, Apartment $apartment, ApartmentImage $image): RedirectResponse
    {
        $this->authorize('update', $apartment);

        if ($image->apartment_id !== $apartment->id) {
            abort(404);
        }

        $request->validate([
            'alt_text' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'replace_image' => ['nullable', 'image', $this->imageMaxRule()],
        ]);

        if ($request->hasFile('replace_image')) {
            try {
                $newPath = AdminUploadedImage::storeAsWebp($request->file('replace_image'), "apartments/{$apartment->id}/gallery", 'public');
            } catch (Throwable $e) {
                report($e);

                return back()->withErrors(['replace_image' => __('Image could not be processed. Use JPEG or PNG.')]);
            }
            Storage::disk('public')->delete($image->path);
            $image->path = $newPath;
        }

        $image->alt_text = $request->input('alt_text', $image->alt_text ?? '');
        $image->sort_order = (int) $request->input('sort_order');
        $image->save();

        return back()->with('success', __('Image updated.'));
    }

    public function moveUp(Apartment $apartment, ApartmentImage $image): RedirectResponse
    {
        $this->authorize('update', $apartment);

        if ($image->apartment_id !== $apartment->id) {
            abort(404);
        }

        $prev = $apartment->images()
            ->where('sort_order', '<', $image->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($prev) {
            $tmp = $image->sort_order;
            $image->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $tmp]);
        }

        return back()->with('success', __('Order updated.'));
    }

    public function moveDown(Apartment $apartment, ApartmentImage $image): RedirectResponse
    {
        $this->authorize('update', $apartment);

        if ($image->apartment_id !== $apartment->id) {
            abort(404);
        }

        $next = $apartment->images()
            ->where('sort_order', '>', $image->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            $tmp = $image->sort_order;
            $image->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $tmp]);
        }

        return back()->with('success', __('Order updated.'));
    }

    public function updateOrder(Request $request, Apartment $apartment): RedirectResponse
    {
        $this->authorize('update', $apartment);

        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'exists:apartment_images,id'],
        ]);

        foreach ($request->input('order', []) as $position => $id) {
            ApartmentImage::query()->where('apartment_id', $apartment->id)->whereKey($id)->update(['sort_order' => $position]);
        }

        return back()->with('success', __('Order updated.'));
    }

    public function destroy(Apartment $apartment, ApartmentImage $image): RedirectResponse
    {
        $this->authorize('update', $apartment);

        if ($image->apartment_id !== $apartment->id) {
            abort(404);
        }

        Storage::disk('public')->delete($image->path);
        $image->delete();

        return back()->with('success', __('Image removed.'));
    }
}
