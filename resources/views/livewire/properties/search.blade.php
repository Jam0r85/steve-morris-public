<div class="max-w-7xl mx-auto px-4 py-6">
    {{-- Top thin progress bar --}}
    <div wire:loading.delay.short class="fixed left-0 top-0 z-50 h-1 w-full bg-gradient-to-r from-blue-500 via-blue-400 to-blue-600 animate-pulse"></div>

    {{-- Filters --}}
    <x-properties.filters
        :channel="$channel"
        :price-min="$priceMin"
        :price-max="$priceMax"
        :has-active-filters="$this->hasActiveFilters"
    />

    {{-- Preconnect image hosts --}}
    <x-properties.preconnect :properties="$properties" />

    {{-- Loading skeletons --}}
    <div wire:loading.class.remove="hidden" wire:target="bedrooms,priceMin,priceMax,sort,includeInactive,search" class="hidden">
        <x-properties.skeletons :count="6" />
    </div>

    {{-- Results grid --}}
    <div wire:loading.remove wire:target="bedrooms,priceMin,priceMax,sort,includeInactive,search">
        <x-properties.grid :properties="$properties" :channel="$channel" />
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $properties->links() }}
    </div>
</div>
