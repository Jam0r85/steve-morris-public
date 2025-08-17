@props([
    'channel' => 'lettings',
    'priceMin' => null,
    'priceMax' => null,
    'hasActiveFilters' => false,
])

@php
    $isLettings   = ($channel === 'lettings');
    $minRange     = $isLettings ? range(0, 2000, 50) : range(50000, 1500000, 100000);
    $maxRange     = $minRange;
    $capValue     = $isLettings ? 2001 : 1500001;
    $capLabel     = $isLettings ? '£2,000+' : '£1,500,000+';

    $minSelected  = ($priceMin === '' || $priceMin === null) ? null : (int) $priceMin;
    $maxSelected  = ($priceMax === '' || $priceMax === null) ? null : (int) $priceMax;

    $limitForMin  = is_null($maxSelected) ? null : ($maxSelected === $capValue ? max($minRange) : $maxSelected);
    $minOptions   = array_values(array_filter($minRange, fn ($p) => is_null($limitForMin) ? true : $p < $limitForMin));
    if (!is_null($minSelected) && !in_array($minSelected, $minOptions, true)) { $minOptions[] = $minSelected; sort($minOptions, SORT_NUMERIC); }

    $maxOptions   = array_values(array_filter($maxRange, fn ($p) => is_null($minSelected) ? true : $p > $minSelected));
    if (!is_null($maxSelected) && $maxSelected !== $capValue && !in_array($maxSelected, $maxOptions, true)) { $maxOptions[] = $maxSelected; sort($maxOptions, SORT_NUMERIC); }
@endphp

<flux:card class="mb-6">
    <div class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
            {{-- Row 1 --}}
            <flux:field class="col-span-1">
                <flux:label>Min Bedrooms</flux:label>
                <flux:select wire:model.live="bedrooms">
                    <flux:select.option value="">Any</flux:select.option>
                    <flux:select.option value="1">1+</flux:select.option>
                    <flux:select.option value="2">2+</flux:select.option>
                    <flux:select.option value="3">3+</flux:select.option>
                    <flux:select.option value="4">4+</flux:select.option>
                </flux:select>
                <flux:error name="bedrooms" />
            </flux:field>

            <flux:field class="col-span-1">
                <flux:label>Min Price</flux:label>
                <flux:select wire:model.live="priceMin">
                    <flux:select.option value="">Any</flux:select.option>
                    @foreach ($minOptions as $price)
                        <flux:select.option value="{{ $price }}">£{{ number_format($price) }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="priceMin" />
            </flux:field>

            <flux:field class="col-span-1">
                <flux:label>Max Price</flux:label>
                <flux:select wire:model.live="priceMax">
                    <flux:select.option value="">Any</flux:select.option>
                    @foreach ($maxOptions as $price)
                        <flux:select.option value="{{ $price }}">£{{ number_format($price) }}</flux:select.option>
                    @endforeach
                    <flux:select.option value="{{ $capValue }}">{{ $capLabel }}</flux:select.option>
                </flux:select>
                <flux:error name="priceMax" />
            </flux:field>

            <flux:field class="col-span-1">
                <flux:label>Sort by</flux:label>
                <flux:select variant="listbox" wire:model.live="sort">
                    <flux:select.option value="newest">Newest</flux:select.option>
                    <flux:select.option value="price_asc"><div class="flex items-center gap-2"><flux:icon.arrow-up variant="mini" class="text-zinc-400" /> Price</div></flux:select.option>
                    <flux:select.option value="price_desc"><div class="flex items-center gap-2"><flux:icon.arrow-down variant="mini" class="text-zinc-400" /> Price</div></flux:select.option>
                    <flux:select.option value="beds_asc"><div class="flex items-center gap-2"><flux:icon.arrow-up variant="mini" class="text-zinc-400" /> Beds</div></flux:select.option>
                    <flux:select.option value="beds_desc"><div class="flex items-center gap-2"><flux:icon.arrow-down variant="mini" class="text-zinc-400" /> Beds</div></flux:select.option>
                </flux:select>
                <flux:error name="sort" />
            </flux:field>

            {{-- Bottom bar: left switch, right reset --}}
            <div class="col-span-1 md:col-span-4">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-3">
                        <flux:label class="m-0">Include inactive</flux:label>
                        <flux:switch wire:model.live="includeInactive" :checked="$includeInactive ?? false" />
                        <flux:error name="includeInactive" />
                    </div>

                    <div class="flex justify-stretch md:justify-end">
                        <x-flux::button
                            type="button"
                            variant="danger"
                            class="w-full md:w-auto disabled:opacity-50 disabled:cursor-not-allowed transition-opacity"
                            wire:click="resetFilters"
                            :disabled="! $hasActiveFilters"
                            wire:loading.attr="disabled"
                            wire:target="resetFilters"
                            aria-disabled="{{ $hasActiveFilters ? 'false' : 'true' }}"
                            title="{{ $hasActiveFilters ? 'Reset all filters' : 'No filters to reset' }}"
                        >
                            Reset Filters
                        </x-flux::button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</flux:card>
