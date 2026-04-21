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
            ->get();

        $page = Page::query()->where('slug', 'apartments')->first();

        return view('apartments.index', compact('apartments', 'page'));
    }

    public function show(Apartment $apartment): View
    {
        if (! $apartment->is_active) {
            abort(404);
        }

        $apartment->load(['amenities', 'images', 'seasons']);
        $apartments = Apartment::query()->where('is_active', true)->orderBy('sort_order')->get();

        return view('apartments.show', compact('apartment', 'apartments'));
    }
}
