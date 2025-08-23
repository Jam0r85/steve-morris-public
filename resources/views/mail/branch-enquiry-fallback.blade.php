@component('mail::message')
# Website enquiry (fallback) Street API submission failed. Details below. **Property:**

{{ $property->address_single_line ?? 'N/A' }} **Slug:** {{ $property->slug ?? 'N/A' }} **Provider ID:**

{{ $property->provider_id ?? 'N/A' }}

@component('mail::panel')
{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
@endcomponent
@endcomponent
