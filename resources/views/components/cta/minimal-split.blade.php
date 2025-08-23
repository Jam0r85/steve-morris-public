@props([
    'title' => '',
    'copy' => null,
    'primaryLabel' => null,
    'primaryHref' => '#',
    'primaryExternal' => false,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'secondaryExternal' => false,
])

<section {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-900']) }}>
    <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:flex lg:items-center lg:justify-between lg:px-8">
        <div class="max-w-2xl">
            @if ($title)
                <flux:heading level="h2" class="sm:text-5xl">{{ $title }}</flux:heading>
            @endif

            @if ($copy)
                <flux:text class="mt-2 text-zinc-600 dark:text-zinc-300">{{ $copy }}</flux:text>
            @endif
        </div>
        <div class="mt-10 flex items-center gap-x-6 lg:mt-0 lg:shrink-0">
            @if ($primaryLabel && $primaryHref)
                <flux:link :href="$primaryHref" @if($primaryExternal) external @endif>
                    <flux:button variant="primary">{{ $primaryLabel }}</flux:button>
                </flux:link>
            @endif

            @if ($secondaryLabel && $secondaryHref)
                <flux:link :href="$secondaryHref" @if($secondaryExternal) external @endif>
                    <a class="text-sm/6 font-semibold text-gray-900 dark:text-gray-100">
                        {{ $secondaryLabel }}
                        <span aria-hidden="true">→</span>
                    </a>
                </flux:link>
            @endif
        </div>
    </div>
</section>
