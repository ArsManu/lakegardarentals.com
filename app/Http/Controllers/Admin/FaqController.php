<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Faq::class);

        $faqs = Faq::query()->orderBy('page_slug')->orderBy('sort_order')->paginate(50);

        return view('admin.faqs.index', compact('faqs'));
    }

    public function create(): View
    {
        $this->authorize('create', Faq::class);

        return view('admin.faqs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Faq::class);

        $data = $request->validate([
            'page_slug' => ['nullable', 'string', 'max:64'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        Faq::query()->create($data);

        return redirect()->route('admin.faqs.index')->with('success', __('Created.'));
    }

    public function edit(Faq $faq): View
    {
        $this->authorize('update', $faq);

        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $this->authorize('update', $faq);

        $data = $request->validate([
            'page_slug' => ['nullable', 'string', 'max:64'],
            'question' => ['required', 'string', 'max:500'],
            'answer' => ['required', 'string'],
            'sort_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
        $data['is_active'] = $request->boolean('is_active');

        $faq->update($data);

        return redirect()->route('admin.faqs.index')->with('success', __('Saved.'));
    }

    public function destroy(Faq $faq): RedirectResponse
    {
        $this->authorize('delete', $faq);

        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('success', __('Deleted.'));
    }
}
