<x-layouts.app title="{{ $property->address_single_line }}" description="">
    <div class="mx-auto max-w-7xl px-4 py-6">
        {{-- Back to search (smart) --}}
        @php
            $prev = url()->previous();
            $samePage = $prev === url()->current();
            $external = parse_url($prev, PHP_URL_HOST) && parse_url($prev, PHP_URL_HOST) !== request()->getHost();
            $fallback = url("/properties/{$channel}"); // adjust if your listing URL differs
            $backHref = ! $samePage && ! $external && $prev ? $prev : $fallback;
        @endphp

        <div class="mb-4">
            <a
                href="{{ $backHref }}"
                onclick="if(document.referrer && document.referrer.startsWith(location.origin)){ history.back(); return false; }"
                class="inline-flex items-center gap-1 text-sm text-zinc-600 hover:text-zinc-900"
            >
                <flux:icon.chevron-left class="h-4 w-4" />
                Back to search
            </a>
        </div>

        {{-- Summary header (no card; comfy whitespace) --}}
        <x-property.summary
            :address="$property->address_single_line"
            :type-label="$typeLabel ?? $property->typeLabel()"
            :beds="$property->bedrooms"
            :baths="$property->bathrooms"
            :receptions="$property->receptions"
            :price-text="$priceText ?? $property->priceTextForChannel($channel)"
        />

        {{-- Hero gallery --}}
        <x-property.hero :images="$galleryImages ?? $property->galleryImagesLarge()" />

        {{-- 3-col layout (mobile: 1 col; desktop: left spans 2, right sticky card) --}}
        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            {{-- LEFT (no card; section dividers) --}}
            <div class="lg:col-span-2">
                <x-property.sections :property="$property" :channel="$channel" />
            </div>

            {{-- RIGHT (floating / sticky message card) --}}
            <div class="lg:col-span-1">
                <x-property.cta-card
                    heading="Send us a message"
                    :phone="$property->branch_phone ?? '0121 000 0000'"
                    :brochure-url="$property->brochure_url ?? null"
                />
            </div>
        </div>

        @isset($schema)
            <script type="application/ld+json">
                {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
            </script>
        @endisset
    </div>
</x-layouts.app>
