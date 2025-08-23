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

<section {{ $attributes->merge(['class' => 'relative isolate overflow-hidden bg-white dark:bg-gray-900']) }}>
    <div class="px-6 py-24 sm:py-32 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            @if ($title)
                <flux:heading level="h2" class="sm:text-5xl">{{ $title }}</flux:heading>
            @endif

            @if ($copy)
                <flux:text class="mx-auto mt-6 max-w-xl text-pretty text-gray-600 dark:text-gray-300">
                    {{ $copy }}
                </flux:text>
            @endif

            <div class="mt-10 flex items-center justify-center gap-x-6">
                @if ($primaryLabel && $primaryHref)
                    <flux:link :href="$primaryHref" @if($primaryExternal) external @endif>
                        <flux:button
                            class="!bg-white !text-gray-900 shadow-xs hover:!bg-gray-100 dark:!bg-white/15 dark:!text-white dark:shadow-none dark:inset-ring dark:inset-ring-white/5 dark:hover:!bg-white/20"
                        >
                            {{ $primaryLabel }}
                        </flux:button>
                    </flux:link>
                @endif

                @if ($secondaryLabel && $secondaryHref)
                    <flux:link :href="$secondaryHref" @if($secondaryExternal) external @endif>
                        <a
                            class="text-sm/6 font-semibold text-gray-900 hover:opacity-80 dark:text-white dark:hover:text-gray-300"
                        >
                            {{ $secondaryLabel }}
                            <span aria-hidden="true">→</span>
                        </a>
                    </flux:link>
                @endif
            </div>
        </div>
    </div>

    <svg
        viewBox="0 0 1024 1024"
        aria-hidden="true"
        class="absolute top-1/2 left-1/2 -z-10 size-256 -translate-x-1/2 mask-[radial-gradient(closest-side,white,transparent)]"
    >
        <circle r="512" cx="512" cy="512" fill="url(#cta-light-radial)" fill-opacity="0.7" />
        <defs>
            <radialGradient id="cta-light-radial">
                <stop stop-color="#7775D6" />
                <stop offset="1" stop-color="#E935C1" />
            </radialGradient>
        </defs>
    </svg>
</section>
