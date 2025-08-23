<div class="bg-white dark:bg-zinc-900">
    <x-section>
        <x-slot:heading>
            <x-heading.main>
                {{ $channel === 'lettings' ? 'Properties to Let' : 'Properties for Sale' }}
            </x-heading.main>
        </x-slot>

        <x-slot:content>
            {{-- Top thin progress bar --}}
            <div
                wire:loading.delay.short
                class="fixed top-0 left-0 z-50 h-1 w-full animate-pulse bg-gradient-to-r from-blue-500 via-blue-400 to-blue-600"
            ></div>

            {{-- Preconnect image hosts --}}
            <x-properties.preconnect :properties="$properties" />

            <div class="mb-6">
                <x-properties.filters
                    :channel="$channel"
                    :price-min="$priceMin"
                    :price-max="$priceMax"
                    :has-active-filters="$this->hasActiveFilters"
                />
            </div>

            {{-- Results grid --}}
            <div wire:loading.remove wire:target="bedrooms,priceMin,priceMax,sort,includeInactive,search">
                <x-properties.grid :properties="$properties" :channel="$channel" />
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $properties->links() }}
            </div>

            @push('structured-data')
                {!!
                    app(\App\Support\StructuredData::class)->propertiesCollectionPage(
                        orgName: config('app.name', 'Steve Morris & Son LLP'),
                        orgUrl: url('/'),
                        pageUrl: request()->url(), // e.g. /lettings or /sales
                        channel: $channel,
                        items: $properties->getCollection(), // first page items only
                        pageTitle: $channel === 'lettings' ? 'Properties to Let' : 'Properties for Sale',
                        pageDescription: 'Browse available properties and filter by price, bedrooms, and features.',
                    )
                !!}
            @endpush
        </x-slot>
    </x-section>
</div>
