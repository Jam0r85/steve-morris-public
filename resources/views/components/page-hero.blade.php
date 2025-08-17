@props([
    'title',
    'intro' => null,
])

<section class="bg-accent overflow-hidden">
    <flux:main container>
        <flux:heading level="1" class="text-accent-foreground text-3xl leading-tight sm:text-5xl">
            {{ $title }}
        </flux:heading>

        @if ($intro)
            <flux:text class="text-accent-foreground mt-4 max-w-xl text-lg">
                {{ $intro }}
            </flux:text>
        @endif
    </flux:main>
</section>
