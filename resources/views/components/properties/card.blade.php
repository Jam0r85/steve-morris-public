@props([
    'property',
    'channel',
])

@php
    $img = $property->primaryImageUrl('medium');
    $isSales = $channel === 'sales';
    $price = $isSales ? $property->price_sales : $property->price_lettings;
    $priceText = $isSales
        ? (! is_null($price)
            ? '£' . number_format($price)
            : 'POA')
        : (! is_null($price)
            ? '£' . number_format($price) . ' ' . ($property->rent_frequency ?? 'pcm')
            : 'POA');

    $typeRaw = $property->property_type ?? ($property->type ?? null);
    $typeLbl = $typeRaw ? ucwords(str_replace('_', ' ', (string) $typeRaw)) : ($isSales ? 'For Sale' : 'To Let');

    $availability = $property->is_active ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut';
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'RealEstateListing',
        'name' => $property->address_single_line,
        'url' => route('properties.show', [
            'channel' => $channel,
            'slug' => $property->slug,
            'property' => $property->slug_id,
        ]),
        'image' => $img ?: null,
        'category' => $typeLbl,
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => $property->address_single_line,
            'addressLocality' => $property->address_town ?? null,
            'postalCode' => $property->address_postcode ?? null,
            'addressCountry' => 'GB',
        ],
        'numberOfRooms' => (int) ($property->bedrooms ?? 0),
        'offers' => array_filter([
            '@type' => 'Offer',
            'price' => is_numeric($price) ? (float) $price : null,
            'priceCurrency' => 'GBP',
            'availability' => $availability,
            'priceSpecification' =>
                ! $isSales && is_numeric($price)
                    ? [
                        '@type' => 'UnitPriceSpecification',
                        'price' => (float) $price,
                        'priceCurrency' => 'GBP',
                        'unitText' => strtoupper($property->rent_frequency ?? 'pcm'),
                    ]
                    : null,
        ]),
    ];
@endphp

<a
    href="{{
        route('properties.show', [
            'channel' => $channel,
            'slug' => $property->slug,
            'property' => $property->slug_id,
        ])
    }}"
    class="group block h-full rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none"
    title="View {{ $property->address_single_line }} {{ $property->is_active ? '' : '(' . ($channel === 'sales' ? 'Sold STC' : 'Applied For') . ')' }}"
    aria-label="View {{ $property->address_single_line }} details"
>
    <article class="group block h-full" aria-labelledby="prop-{{ $property->id }}-title">
        <flux:card class="relative overflow-hidden p-0 transition-shadow duration-200 group-hover:shadow-md">
            {{-- Promo badges (only when active) --}}
            @if ($property->isNewListing() || $property->isReduced())
                <div class="absolute top-2 right-2 z-20 space-y-1">
                    @if ($property->isNewListing())
                        <span
                            class="inline-block rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] font-semibold text-white"
                        >
                            New
                        </span>
                    @endif

                    @if ($property->isReduced())
                        <span
                            class="inline-block rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-semibold text-white"
                        >
                            Reduced
                        </span>
                    @endif
                </div>
            @endif

            {{-- Inactive ribbon (Sold STC / Applied For) --}}
            @if (! $property->isActive())
                <div
                    class="absolute top-4 -left-12 z-30 w-40 -rotate-45 transform bg-red-600 py-1 text-center text-xs font-semibold text-white shadow-lg"
                >
                    {{ $isSales ? 'Sold STC' : 'Applied For' }}
                </div>
            @endif

            {{-- Gradient hover overlay (sits below badges/ribbon) --}}
            <div
                class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center bg-gradient-to-t from-black/30 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
            >
                <span class="rounded bg-white/90 px-3 py-1 text-sm font-medium text-zinc-800 shadow">View More</span>
            </div>

            <div class="relative w-full overflow-hidden">
                <div class="aspect-[4/3]">
                    @if ($img)
                        <img
                            src="{{ $img }}"
                            alt="{{ $property->address_single_line }}"
                            loading="lazy"
                            decoding="async"
                            class="h-full w-full object-cover"
                        />
                    @else
                        <div class="flex h-full w-full items-center justify-center bg-zinc-200 text-zinc-500">
                            No image
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-1 flex-col p-4">
                <x-flux::heading level="6" size="xs" class="mb-1 tracking-wide text-zinc-500 uppercase">
                    {{ $typeLbl }}
                </x-flux::heading>

                <x-flux::heading id="prop-{{ $property->id }}-title" level="3" size="lg" class="mb-1 font-semibold">
                    {{ $property->address_single_line }}
                </x-flux::heading>

                <x-flux::heading level="6" size="sm" class="mb-2 font-normal text-zinc-500">
                    {{ $property->bedrooms }} bed
                    @if ($property->bathrooms)
                        · {{ $property->bathrooms }} bath
                    @endif
                </x-flux::heading>

                <x-flux::heading level="4" size="md" class="mt-auto font-bold text-blue-600">
                    {{ $priceText }}
                </x-flux::heading>
            </div>

            <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
        </flux:card>
    </article>
</a>
