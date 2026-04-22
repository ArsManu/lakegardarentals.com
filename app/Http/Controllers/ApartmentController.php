<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Page;
use Illuminate\View\View;

class ApartmentController extends Controller
{
    public function index(): View
    {
        $apartments = Apartment::query()
            ->where('is_active', true)
            ->with(['amenities', 'images'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Apartment $a) => $a->withTranslatedAmenities());

        $page = Page::query()->where('slug', 'apartments')->first()?->displayForLocale();

        return view('apartments.index', compact('apartments', 'page'));
    }

    public function show(Apartment $apartment): View
    {
        return $this->renderApartment($apartment);
    }

    public function showInLocale(string $locale, Apartment $apartment): View
    {
        return $this->renderApartment($apartment);
    }

    private function renderApartment(Apartment $apartment): View
    {
        if (! $apartment->is_active) {
            abort(404);
        }

        $apartment = $apartment->load(['amenities', 'images', 'seasons'])->withTranslatedAmenities();
        $apartments = Apartment::query()->where('is_active', true)->orderBy('sort_order')->get()
            ->map(fn (Apartment $a) => $a->withTranslatedAmenities());

        return view('apartments.show', compact('apartment', 'apartments'));
    }
}
