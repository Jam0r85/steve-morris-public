@props([
    'title',
    'body',
    'bodyTextSize' => 'sm',
])

<flux:card size="sm">
    <flux:heading size="lg">
        {{ $title }}
    </flux:heading>
    <flux:text size="{{ $bodyTextSize }}" class="mt-2">{{ $body }}</flux:text>
</flux:card>
