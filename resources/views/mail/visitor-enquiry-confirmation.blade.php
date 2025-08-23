@component('mail::message')
# Thanks {{ $firstName }},

We’ve received your enquiry{{ $property->address_single_line ? ' about ' . $property->address_single_line : '' }}.  
A member of our team will be in touch as soon as possible.

If you need anything urgently, call us on **{{ $property->branch_phone ?? '0121 000 0000' }}**.

Thanks,  
**{{ config('app.name') }}**
@endcomponent
