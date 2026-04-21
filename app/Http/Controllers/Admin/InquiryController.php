<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Inquiry::class);

        $q = Inquiry::query()->with('apartment')->latest();

        if ($request->filled('status')) {
            $q->where('status', $request->string('status'));
        }
        if ($request->filled('type')) {
            $q->where('type', $request->string('type'));
        }

        $inquiries = $q->paginate(30)->withQueryString();

        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function show(Inquiry $inquiry): View
    {
        $this->authorize('view', $inquiry);

        $inquiry->markRead();
        $inquiry->load('apartment');

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('update', $inquiry);

        $data = $request->validate([
            'status' => ['required', 'in:new,contacted,closed'],
        ]);

        $inquiry->update($data);

        return redirect()->route('admin.inquiries.show', $inquiry)->with('success', __('Updated.'));
    }

    public function destroy(Inquiry $inquiry): RedirectResponse
    {
        $this->authorize('delete', $inquiry);

        $inquiry->delete();

        return redirect()->route('admin.inquiries.index')->with('success', __('Deleted.'));
    }
}
