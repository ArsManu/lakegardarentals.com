<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestimonialController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Testimonial::class);

        $testimonials = Testimonial::query()->orderBy('sort_order')->paginate(40);

        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create(): View
    {
        $this->authorize('create', Testimonial::class);

        return view('admin.testimonials.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Testimonial::class);

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_location' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['integer', 'min:0'],
            'is_published' => ['boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');

        Testimonial::query()->create($data);

        return redirect()->route('admin.testimonials.index')->with('success', __('Created.'));
    }

    public function edit(Testimonial $testimonial): View
    {
        $this->authorize('update', $testimonial);

        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial): RedirectResponse
    {
        $this->authorize('update', $testimonial);

        $data = $request->validate([
            'author_name' => ['required', 'string', 'max:255'],
            'author_location' => ['nullable', 'string', 'max:255'],
            'quote' => ['required', 'string'],
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'],
            'sort_order' => ['integer', 'min:0'],
            'is_published' => ['boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', __('Saved.'));
    }

    public function destroy(Testimonial $testimonial): RedirectResponse
    {
        $this->authorize('delete', $testimonial);

        $testimonial->delete();

        return redirect()->route('admin.testimonials.index')->with('success', __('Deleted.'));
    }
}
