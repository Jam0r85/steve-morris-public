@props([
    // Accepts a single JSON-LD array OR an array of JSON-LD arrays
    'data' => [],
])

@php
    $items = $data;

    // If it's a single schema object (has @context), normalise to an array of one
    $isSingle = is_array($items) && isset($items['@context']);
    if ($isSingle) {
        $items = [$items];
    }

    // If it's not a list (no numeric 0) but still a valid object, also normalise
    if (is_array($items) && ! isset($items[0]) && ! isset($items['@context'])) {
        $items = [$items];
    }
@endphp

@foreach ($items as $item)
    @if (is_array($item))
        <script type="application/ld+json">
            {!! json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif
@endforeach
