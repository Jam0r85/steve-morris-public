@props(['property', 'channel'])

@php
    $img     = $property->primaryImageUrl('medium');
    $isSales = ($channel === 'sales');
    $price   = $isSales ? $property->price_sales : $property->price_lettings;
    $priceText = $isSales
        ? (!is_null($price) ? '£'.number_format($price) : 'POA')
        : (!is_null($price) ? '£'.number_format($price).' '.($property->rent_frequency ?? 'pcm') : 'POA');

    $typeRaw = $property->property_type ?? $property->type ?? null;
    $typeLbl = $typeRaw ? ucwords(str_replace('_',' ', (string)$typeRaw)) : ($isSales ? 'For Sale' : 'To Let');

    $availability = $property->is_active ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut';
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'RealEstateListing',
        'name'     => $property->address_single_line,
        'url'      => route('properties.show', ['channel'=>$channel,'slug'=>$property->slug,'slug_id'=>$property->slug_id]),
        'image'    => $img ?: null,
        'category' => $typeLbl,
        'address'  => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $property->address_single_line,
            'addressLocality' => $property->address_town ?? null,
            'postalCode'      => $property->address_postcode ?? null,
            'addressCountry'  => 'GB',
        ],
        'numberOfRooms' => (int) ($property->bedrooms ?? 0),
        'offers' => array_filter([
            '@type'         => 'Offer',
            'price'         => is_numeric($price) ? (float) $price : null,
            'priceCurrency' => 'GBP',
            'availability'  => $availability,
            'priceSpecification' => !$isSales && is_numeric($price) ? [
                '@type'         => 'UnitPriceSpecification',
                'price'         => (float) $price,
                'priceCurrency' => 'GBP',
                'unitText'      => strtoupper($property->rent_frequency ?? 'pcm'),
            ] : null,
        ]),
    ];
@endphp

<a
    href="{{ route('properties.show', ['channel'=>$channel,'slug'=>$property->slug,'slug_id'=>$property->slug_id]) }}"
    class="group block h-full"
    title="View {{ $property->address_single_line }} ({{ $channel }})"
    aria-label="View {{ $property->address_single_line }} details"
>
    <flux:card class="relative h-full flex flex-col overflow-hidden transition-shadow duration-200 group-hover:shadow-md p-0">
        @if($property->is_active == 0)
            <div class="absolute top-4 -left-12 w-40 bg-red-600 text-white text-xs font-semibold text-center py-1 transform -rotate-45 shadow-lg">
                {{ $isSales ? 'Sold STC' : 'Applied For' }}
            </div>
        @endif

        @if ($img)
            <img src="{{ $img }}" alt="{{ $property->address_single_line }}" loading="lazy" decoding="async" class="w-full h-48 object-cover" />
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center text-gray-500">No image</div>
        @endif

        <div class="p-4 flex flex-col flex-1">
            <div class="text-[11px] font-medium uppercase tracking-wide text-zinc-500 mb-1">
                {{ $typeLbl }}
            </div>

            <h3 class="text-lg font-semibold mb-1">
                {{ $property->address_single_line }}
            </h3>

            <p class="text-sm text-gray-500 mb-2">
                {{ $property->bedrooms }} bed · {{ $property->bathrooms }} bath
            </p>

            <p class="text-blue-600 font-bold mt-auto">
                {{ $priceText }}
            </p>
        </div>

        <script type="application/ld+json">
            {!! json_encode($schema, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
        </script>
    </flux:card>
</a>
