<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AmenityController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Amenity::class);

        $amenities = Amenity::query()->orderBy('sort_order')->orderBy('name')->paginate(40);

        return view('admin.amenities.index', compact('amenities'));
    }

    public function create(): View
    {
        $this->authorize('create', Amenity::class);

        return view('admin.amenities.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Amenity::class);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:amenities,slug'],
            'icon_key' => ['nullable', 'string', 'max:64'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['slug'] = $this->uniqueSlug($data['slug']);

        Amenity::query()->create($data);

        return redirect()->route('admin.amenities.index')->with('success', __('Created.'));
    }

    public function edit(Amenity $amenity): View
    {
        $this->authorize('update', $amenity);

        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity): RedirectResponse
    {
        $this->authorize('update', $amenity);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:amenities,slug,'.$amenity->id],
            'icon_key' => ['nullable', 'string', 'max:64'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $amenity->update($data);

        return redirect()->route('admin.amenities.index')->with('success', __('Saved.'));
    }

    public function destroy(Amenity $amenity): RedirectResponse
    {
        $this->authorize('delete', $amenity);

        $amenity->delete();

        return redirect()->route('admin.amenities.index')->with('success', __('Deleted.'));
    }

    private function uniqueSlug(string $slug): string
    {
        $base = $slug;
        $candidate = $base;
        $i = 1;
        while (Amenity::query()->where('slug', $candidate)->exists()) {
            $candidate = $base.'-'.$i;
            $i++;
        }

        return $candidate;
    }
}
