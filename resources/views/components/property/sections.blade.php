@props([
    "property",
    "channel" => "lettings",
    //'sales'|'lettings',
])

@php
    $descHtml = $property->descriptionHtmlForChannel($channel);
    ["derived" => $derived, "additional" => $additional] = $property->featuresForDisplay($channel);

    $floorplans = $property->floorplans();
    $hasMap = filled($property->lat) && filled($property->lng);
    $hasVideo = filled($property->video_url ?? null);
@endphp

<div class="divide-y divide-zinc-200">
    {{-- Description (HTML) --}}
    @if ($descHtml)
        <x-property.section id="description" title="Property Description">
            <div class="leading-8">
                <flux:text>
                    {!! $descHtml !!}
                </flux:text>
            </div>
        </x-property.section>
    @endif

    @php
        ["derived" => $derived, "additional" => $additional] = $property->featuresForDisplay($channel);
        $featuresAll = array_merge($derived, $additional);
    @endphp

    @if (! empty($featuresAll))
        <x-property.section id="features" title="Features">
            <ul class="grid grid-cols-1 gap-x-8 gap-y-4 sm:grid-cols-2">
                @foreach ($featuresAll as $item)
                    <li class="flex items-center gap-4 md:gap-5">
                        <x-flux::icon
                            name="{{ $item['icon'] }}"
                            variant="solid"
                            class="h-6 w-6 shrink-0 text-zinc-500 md:h-7 md:w-7"
                        />
                        <x-flux::heading
                            level="6"
                            size="lg"
                            class="m-0 inline-flex flex-wrap items-baseline gap-x-1 font-normal"
                        >
                            <span>
                                {{ $item["label"] }}@if (! empty($item["value"])):
                                @endif
                            </span>
                            @if (! empty($item["value"]))
                                <span class="font-bold">{{ $item["value"] }}</span>
                            @endif
                        </x-flux::heading>
                    </li>
                @endforeach
            </ul>
        </x-property.section>
    @endif

    {{-- Floorplans (Flux modal) --}}
    @php
        $planItems = $floorplans
            ->map(function ($plan, $i) use ($property) {
                $src = $plan->url_large ?? ($plan->url_full ?? ($plan->url ?? null));
                return $src
                    ? [
                        "url" => $src,
                        "alt" => "Floorplan " . ($i + 1) . " for " . ($property->address_single_line ?? "property"),
                        "label" => "View floorplan " . ($i + 1),
                    ]
                    : null;
            })
            ->filter()
            ->values();
    @endphp

    @if ($planItems->isNotEmpty())
        <x-property.section id="floorplans" title="Floorplans">
            <ul class="space-y-2">
                @foreach ($planItems as $i => $p)
                    <li>
                        <flux:modal.trigger name="floor-plan-{{ $i }}">
                            <flux:link class="cursor-pointer">
                                {{ $p["label"] }}
                            </flux:link>
                        </flux:modal.trigger>

                        <flux:modal name="floor-plan-{{ $i }}" size="fullscreen">
                            <div class="flex h-full w-full items-center justify-center p-4">
                                <img
                                    src="{{ $p['url'] }}"
                                    alt="{{ $p['alt'] }}"
                                    class="max-h-[90vh] w-full object-contain"
                                    loading="eager"
                                    decoding="async"
                                />
                            </div>
                        </flux:modal>
                    </li>
                @endforeach
            </ul>
        </x-property.section>
    @endif

    {{-- Map --}}
    @if ($hasMap)
        <x-property.section id="map" title="Location">
            <div class="aspect-[4/3] w-full overflow-hidden rounded-lg bg-zinc-100">
                <iframe
                    class="h-full w-full"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    src="https://maps.google.com/maps?q={{ $property->lat }},{{ $property->lng }}&z=15&output=embed"
                ></iframe>
            </div>
        </x-property.section>
    @endif

    {{-- Video (optional) --}}
    @if ($hasVideo)
        <section id="video" class="py-10">
            <x-flux::heading level="2" size="lg" class="mb-3">Video</x-flux::heading>
            <div class="aspect-video overflow-hidden rounded-lg bg-black">
                <iframe src="{{ $property->video_url }}" class="h-full w-full" allowfullscreen loading="lazy"></iframe>
            </div>
        </section>
    @endif
</div>
