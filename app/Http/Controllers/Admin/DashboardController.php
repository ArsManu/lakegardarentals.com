<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Inquiry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalInquiries = Inquiry::query()->count();
        $newInquiries = Inquiry::query()->where('status', Inquiry::STATUS_NEW)->count();
        $apartmentCount = Apartment::query()->count();
        $recent = Inquiry::query()->with('apartment')->latest()->limit(10)->get();

        return view('admin.dashboard', compact('totalInquiries', 'newInquiries', 'apartmentCount', 'recent'));
    }
}
