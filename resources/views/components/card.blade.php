@props([
    'title',
    'body',
    'bodyTextSize' => null,
])

<flux:card>
    <flux:heading size="lg">
        {{ $title }}
    </flux:heading>
    <flux:text size="{{ $bodyTextSize }}" class="mt-2">{{ $body }}</flux:text>
</flux:card>
