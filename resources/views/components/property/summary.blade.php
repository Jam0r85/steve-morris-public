@props([
    "address" => "—",
    "typeLabel" => "Property",
    "beds" => null,
    "baths" => null,
    "receptions" => null,
    "priceText" => "POA",
])

<div class="py-3 md:py-5">
    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
        <div>
            <x-flux::heading level="1" size="xl" class="font-semibold">
                {{ $address }}
            </x-flux::heading>

            <div class="mt-1 flex flex-wrap items-center gap-x-4 text-zinc-600">
                {{-- Bedrooms (Heroicons: home) --}}
                @if ($beds)
                    <flux:text class="inline-flex items-center gap-1">{{ $beds }} bed</flux:text>
                @endif

                {{-- Bathrooms (text only) --}}
                @if ($baths)
                    <flux:text class="inline-flex items-center gap-1">{{ $baths }} bath</flux:text>
                @endif

                {{-- Receptions (Heroicons: users) --}}
                @if ($receptions)
                    <flux:text class="inline-flex items-center gap-1">
                        {{ $receptions }} reception{{ $receptions > 1 ? "s" : "" }}
                    </flux:text>
                @endif
            </div>
        </div>

        <x-flux::heading level="2" size="xl" class="text-accent text-3xl font-bold">
            {{ $priceText }}
        </x-flux::heading>
    </div>
</div>
