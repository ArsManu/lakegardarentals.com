<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'apartment_id' => ['nullable', 'exists:apartments,id'],
            'check_in' => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1', 'max:30'],
            'message' => ['nullable', 'string', 'max:5000'],
            'consent' => ['accepted'],
            'website' => ['prohibited'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $aptId = $this->input('apartment_id');
            if (! $aptId) {
                return;
            }
            $apt = \App\Models\Apartment::query()->find($aptId);
            if ($apt && (int) $this->input('guests') > $apt->max_guests) {
                $validator->errors()->add('guests', __('The number of guests cannot exceed this apartment\'s capacity.'));
            }
        });
    }
}
