@props([
    'number' => null,
    'title' => '',
    'copy' => '',
    'icon' => 'calendar',
    'span' => false,
])

@php
    $col = $span ? 'lg:col-span-2' : '';
@endphp

<div {{ $attributes->merge(['class' => "relative pl-16 $col"]) }}>
    <dt>
        <div class="bg-accent absolute top-0 left-0 flex size-10 items-center justify-center rounded-lg">
            <flux:icon :name="$icon" class="size-6 text-white" />
        </div>

        <flux:heading level="5" size="xl">
            {{ $title }}
        </flux:heading>
    </dt>

    <dd class="mt-2">
        <flux:text>
            {{ $copy }}
        </flux:text>
    </dd>
</div>
