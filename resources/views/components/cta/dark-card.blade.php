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

<x-section>
    <div
        class="relative isolate overflow-hidden bg-gray-900 px-6 py-24 text-center shadow-2xl sm:rounded-3xl sm:px-16 dark:bg-gray-800 dark:shadow-none dark:after:pointer-events-none dark:after:absolute dark:after:inset-0 dark:after:inset-ring dark:after:inset-ring-white/10 dark:after:sm:rounded-3xl"
    >
        @if ($title)
            <flux:heading level="2" size="xl" class="text-white sm:text-5xl">{{ $title }}</flux:heading>
        @endif

        @if ($copy)
            <flux:text class="mx-auto mt-6 max-w-xl text-lg/8 text-pretty text-gray-300">{{ $copy }}</flux:text>
        @endif

        <div class="mt-10 flex items-center justify-center gap-x-6">
            @if ($primaryLabel && $primaryHref)
                @if ($primaryExternal)
                    <flux:link href="{{ $primaryHref }}" external>
                        <flux:button class="!bg-white !text-gray-900 hover:!bg-gray-100">
                            {{ $primaryLabel }}
                        </flux:button>
                    </flux:link>
                @else
                    <flux:link href="{{ $primaryHref }}">
                        <flux:button class="!bg-white !text-gray-900 hover:!bg-gray-100">
                            {{ $primaryLabel }}
                        </flux:button>
                    </flux:link>
                @endif
            @endif

            @if ($secondaryLabel && $secondaryHref)
                @if ($secondaryExternal)
                    <flux:link href="{{ $secondaryHref }}" external>
                        <span class="text-sm/6 font-semibold text-white">
                            {{ $secondaryLabel }}
                            <span aria-hidden="true">→</span>
                        </span>
                    </flux:link>
                @else
                    <flux:link href="{{ $secondaryHref }}">
                        <span class="text-sm/6 font-semibold text-white">
                            {{ $secondaryLabel }}
                            <span aria-hidden="true">→</span>
                        </span>
                    </flux:link>
                @endif
            @endif
        </div>

        <svg
            viewBox="0 0 1024 1024"
            aria-hidden="true"
            class="absolute top-1/2 left-1/2 -z-10 size-256 -translate-x-1/2 mask-[radial-gradient(closest-side,white,transparent)]"
        >
            <circle r="512" cx="512" cy="512" fill="url(#cta-dark-card-radial)" fill-opacity="0.7" />
            <defs>
                <radialGradient id="cta-dark-card-radial">
                    <stop stop-color="#7775D6" />
                    <stop offset="1" stop-color="#E935C1" />
                </radialGradient>
            </defs>
        </svg>
    </div>
</x-section>
