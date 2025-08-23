@props([
    'header',
    'text',
    'icon',
])

<flux:card {{ $attributes->class('space-y-2 transition-shadow hover:shadow-md') }}>
    <div class="flex items-center gap-3">
        <div class="bg-accent text-accent-foreground flex size-10 items-center justify-center rounded-xl">
            <flux:icon name="{{ $icon }}" class="size-5" />
        </div>
        <flux:heading size="lg" level="5" class="font-semibold">
            {{ $header }}
        </flux:heading>
    </div>

    <flux:text>
        {{ $text }}
    </flux:text>
</flux:card>
