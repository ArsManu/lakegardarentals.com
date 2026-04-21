<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use App\Models\Apartment;
use App\Support\AdminUploadedImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class ApartmentController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Apartment::class);

        $apartments = Apartment::query()->withCount('images')->orderBy('sort_order')->paginate(20);

        return view('admin.apartments.index', compact('apartments'));
    }

    public function create(): View
    {
        $this->authorize('create', Apartment::class);

        $amenities = Amenity::query()->orderBy('sort_order')->orderBy('name')->get();

        return view('admin.apartments.create', compact('amenities'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Apartment::class);

        $data = $this->validated($request);
        unset($data['amenities'], $data['featured_image']);
        $data['is_active'] = $request->boolean('is_active');
        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['name']));

        $apartment = Apartment::query()->create($data);

        if ($request->hasFile('featured_image')) {
            try {
                $path = AdminUploadedImage::storeAsWebp($request->file('featured_image'), "apartments/{$apartment->id}", 'public');
            } catch (Throwable $e) {
                report($e);

                return back()->withErrors(['featured_image' => __('Image could not be processed. Use JPEG or PNG.')])->withInput();
            }
            $apartment->update(['featured_image' => $path]);
        }

        $apartment->amenities()->sync($request->input('amenities', []));

        return redirect()->route('admin.apartments.edit', $apartment)->with('success', __('Apartment created. Add gallery images below.'));
    }

    public function edit(Apartment $apartment): View
    {
        $this->authorize('update', $apartment);

        $amenities = Amenity::query()->orderBy('sort_order')->orderBy('name')->get();
        $apartment->load(['amenities', 'images', 'seasons']);

        return view('admin.apartments.edit', compact('apartment', 'amenities'));
    }

    public function update(Request $request, Apartment $apartment): RedirectResponse
    {
        $this->authorize('update', $apartment);

        $data = $this->validated($request, $apartment->id);
        unset($data['amenities'], $data['featured_image']);
        $data['is_active'] = $request->boolean('is_active');
        if (isset($data['slug']) && $data['slug'] !== $apartment->slug) {
            $data['slug'] = $this->uniqueSlug($data['slug'], $apartment->id);
        }

        if ($request->boolean('remove_featured_image')) {
            if ($apartment->featured_image) {
                Storage::disk('public')->delete($apartment->featured_image);
            }
            $data['featured_image'] = null;
        } elseif ($request->hasFile('featured_image')) {
            try {
                $path = AdminUploadedImage::storeAsWebp($request->file('featured_image'), "apartments/{$apartment->id}", 'public');
            } catch (Throwable $e) {
                report($e);

                return back()->withErrors(['featured_image' => __('Image could not be processed. Use JPEG or PNG.')])->withInput();
            }
            if ($apartment->featured_image) {
                Storage::disk('public')->delete($apartment->featured_image);
            }
            $data['featured_image'] = $path;
        }

        $apartment->update($data);
        $apartment->amenities()->sync($request->input('amenities', []));

        return redirect()->route('admin.apartments.edit', $apartment)->with('success', __('Saved.'));
    }

    public function destroy(Apartment $apartment): RedirectResponse
    {
        $this->authorize('delete', $apartment);

        foreach ($apartment->images as $image) {
            Storage::disk('public')->delete($image->path);
        }
        if ($apartment->featured_image) {
            Storage::disk('public')->delete($apartment->featured_image);
        }
        Storage::disk('public')->deleteDirectory('apartments/'.$apartment->id);

        $apartment->delete();

        return redirect()->route('admin.apartments.index')->with('success', __('Deleted.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?int $ignoreId = null): array
    {
        $slugRule = ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'];
        if ($ignoreId) {
            $slugRule[] = 'unique:apartments,slug,'.$ignoreId;
        } else {
            $slugRule[] = 'unique:apartments,slug';
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => $slugRule,
            'short_description' => ['required', 'string'],
            'full_description' => ['required', 'string'],
            'ideal_for' => ['nullable', 'string', 'max:255'],
            'max_guests' => ['required', 'integer', 'min:1', 'max:30'],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'bathrooms' => ['required', 'integer', 'min:0', 'max:20'],
            'size_m2' => ['nullable', 'integer', 'min:1', 'max:10000'],
            'price_from' => ['required', 'numeric', 'min:0'],
            'location_text' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:512'],
            'check_in_out_note' => ['nullable', 'string'],
            'availability_note' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'external_listing_url' => ['nullable', 'url', 'max:2048'],
            'license_cir' => ['nullable', 'string', 'max:64'],
            'license_cin' => ['nullable', 'string', 'max:64'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'amenities' => ['array'],
            'amenities.*' => ['exists:amenities,id'],
            'featured_image' => ['nullable', 'image', 'max:'.config('lakegarda.admin_image_max_kb')],
            'remove_featured_image' => ['sometimes', 'boolean'],
        ]);
    }

    private function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $base = $slug ?: 'apartment';
        $candidate = $base;
        $i = 1;
        while (Apartment::query()->where('slug', $candidate)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $candidate = $base.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
