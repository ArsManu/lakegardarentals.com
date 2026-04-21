<x-mail::message>
# New inquiry

**Type:** {{ $inquiry->type }}

**Name:** {{ $inquiry->name }}

**Email:** {{ $inquiry->email }}

**Phone:** {{ $inquiry->phone }}

@if($inquiry->apartment)
**Apartment:** {{ $inquiry->apartment->name }}
@endif

@if($inquiry->check_in)
**Check-in:** {{ $inquiry->check_in->format('Y-m-d') }}
@endif

@if($inquiry->check_out)
**Check-out:** {{ $inquiry->check_out->format('Y-m-d') }}
@endif

@if($inquiry->guests)
**Guests:** {{ $inquiry->guests }}
@endif

@if($inquiry->message)
**Message:**

{{ $inquiry->message }}
@endif

**Source page:** {{ $inquiry->source_page ?? '—' }}

<x-mail::button :url="url('/admin/inquiries/'.$inquiry->id)">
View in admin
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
