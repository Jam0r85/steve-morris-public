@props(['properties'])

@php
    $imageHosts = collect($properties->items() ?? [])
        ->map(fn($p) => method_exists($p, 'primaryImageUrl') ? $p->primaryImageUrl('medium') : null)
        ->filter()
        ->map(fn($url) => parse_url($url, PHP_URL_HOST))
        ->filter()
        ->unique()
        ->values();
@endphp

@push('head')
    @foreach ($imageHosts as $host)
        <link rel="preconnect" href="https://{{ $host }}" crossorigin>
    @endforeach
@endpush
