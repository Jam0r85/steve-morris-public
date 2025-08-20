@props([
    'properties',
    'channel',
])

@if ($properties && $properties->isNotEmpty())
    <div class="grid grid-cols-1 items-stretch gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($properties as $property)
            <x-properties.card :property="$property" :channel="$channel" />
        @endforeach
    </div>
@else
    <div class="flex items-center justify-center">
        <div class="w-full rounded-2xl border-2 border-dashed border-gray-300 bg-zinc-100 p-10 text-center">
            <x-flux::icon name="home" class="mx-auto mb-6 h-14 w-14 text-zinc-400" />
            <x-flux::heading level="2" size="xl" class="text-zinc-700">No properties found</x-flux::heading>
            <p class="mt-3 text-zinc-500">
                Try adjusting your filters or clearing them to see more available listings.
            </p>
            <div class="mt-8">
                <x-flux::button wire:click="resetFilters" variant="primary">Reset Filters</x-flux::button>
            </div>
        </div>
    </div>
@endif
