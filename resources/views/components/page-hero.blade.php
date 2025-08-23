@props([
    'title',
    'intro' => null,
])

<section class="bg-accent overflow-hidden">
    <flux:main container>
        <flux:heading size="xl" level="1" class="text-accent-foreground text-3xl leading-tight sm:text-5xl">
            {{ $title }}
        </flux:heading>

        @if ($intro)
            <div class="leading-6">
                <flux:text class="text-accent-foreground mt-4 max-w-xl text-base">
                    {{ $intro }}
                </flux:text>
            </div>
        @endif

        {{ $slot }}
    </flux:main>
</section>
