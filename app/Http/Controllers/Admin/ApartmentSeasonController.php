<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\ApartmentSeason;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApartmentSeasonController extends Controller
{
    public function store(Request $request, Apartment $apartment): RedirectResponse
    {
        $this->authorize('update', $apartment);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
        ]);

        $data['sort_order'] = (int) $apartment->seasons()->max('sort_order') + 1;
        $apartment->seasons()->create($data);

        return back()->with('success', __('Season added.'));
    }

    public function update(Request $request, Apartment $apartment, ApartmentSeason $season): RedirectResponse
    {
        $this->authorize('update', $apartment);

        if ($season->apartment_id !== $apartment->id) {
            abort(404);
        }

        $data = $request->validate([
            'label' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'price_per_night' => ['required', 'numeric', 'min:0'],
        ]);

        $season->update($data);

        return back()->with('success', __('Season updated.'));
    }

    public function destroy(Apartment $apartment, ApartmentSeason $season): RedirectResponse
    {
        $this->authorize('update', $apartment);

        if ($season->apartment_id !== $apartment->id) {
            abort(404);
        }

        $season->delete();

        return back()->with('success', __('Season removed.'));
    }
}
