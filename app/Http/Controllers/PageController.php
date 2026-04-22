<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        $page = Page::query()->where('slug', 'home')->firstOrFail()->displayForLocale();
        $apartments = Apartment::query()
            ->where('is_active', true)
            ->with(['amenities', 'images'])
            ->orderBy('sort_order')
            ->limit(2)
            ->get()
            ->map(fn (Apartment $a) => $a->withTranslatedAmenities());

        return view('pages.home', compact('page', 'apartments'));
    }

    public function lakeGarda(): View
    {
        $page = Page::query()->where('slug', 'lake-garda')->firstOrFail()->displayForLocale();

        return view('pages.lake-garda', compact('page'));
    }

    public function contact(): View
    {
        $page = Page::query()->where('slug', 'contact')->firstOrFail()->displayForLocale();
        $apartments = Apartment::query()->where('is_active', true)->orderBy('sort_order')->get()
            ->map(fn (Apartment $a) => $a->withTranslatedAmenities());

        return view('pages.contact', compact('page', 'apartments'));
    }
}
