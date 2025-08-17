@props(['properties', 'channel'])

@if ($properties && $properties->isNotEmpty())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 items-stretch">
        @foreach ($properties as $property)
            <x-properties.card :property="$property" :channel="$channel" />
        @endforeach
    </div>
@else
    <div class="flex items-center justify-center">
        <div class="border-2 border-dashed border-gray-300 bg-gray-100 rounded-2xl p-10 text-center w-full">
            <x-flux::icon name="home" class="w-14 h-14 text-gray-400 mx-auto mb-6" />
            <x-flux::heading level="2" size="xl" class="text-gray-700">No properties found</x-flux::heading>
            <p class="mt-3 text-gray-500">Try adjusting your filters or clearing them to see more available listings.</p>
            <div class="mt-8">
                <x-flux::button wire:click="resetFilters" variant="primary">Reset Filters</x-flux::button>
            </div>
        </div>
    </div>
@endif
