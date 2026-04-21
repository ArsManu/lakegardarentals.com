<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingInquiryRequest;
use App\Http\Requests\StoreContactInquiryRequest;
use App\Mail\InquiryReceived;
use App\Models\Inquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class InquiryController extends Controller
{
    public function storeBooking(StoreBookingInquiryRequest $request): RedirectResponse
    {
        $inquiry = Inquiry::query()->create([
            'type' => Inquiry::TYPE_BOOKING,
            'apartment_id' => $request->validated('apartment_id'),
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'phone' => $request->validated('phone'),
            'check_in' => $request->validated('check_in'),
            'check_out' => $request->validated('check_out'),
            'guests' => $request->validated('guests'),
            'message' => $request->validated('message'),
            'consent_at' => now(),
            'status' => Inquiry::STATUS_NEW,
            'source_page' => $request->input('source_page', 'booking-form'),
            'ip' => $request->ip(),
        ]);

        $inquiry->load('apartment');

        Mail::to(config('lakegarda.inquiry_notify_email'))->send(new InquiryReceived($inquiry));

        return redirect()->route('thank-you')->with('inquiry_id', $inquiry->id);
    }

    public function storeContact(StoreContactInquiryRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $inquiry = Inquiry::query()->create([
            'type' => Inquiry::TYPE_CONTACT,
            'apartment_id' => $data['apartment_id'] ?? null,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'check_in' => $data['check_in'] ?? null,
            'check_out' => $data['check_out'] ?? null,
            'guests' => $data['guests'] ?? null,
            'message' => $data['message'] ?? null,
            'consent_at' => now(),
            'status' => Inquiry::STATUS_NEW,
            'source_page' => $request->input('source_page', 'contact-form'),
            'ip' => $request->ip(),
        ]);

        $inquiry->load('apartment');

        Mail::to(config('lakegarda.inquiry_notify_email'))->send(new InquiryReceived($inquiry));

        return redirect()->route('thank-you')->with('inquiry_id', $inquiry->id);
    }

    public function thankYou(): View
    {
        return view('pages.thank-you');
    }
}
