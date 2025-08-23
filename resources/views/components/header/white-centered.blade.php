@props([
    'title',
    'intro',
])

<x-section pad="lg">
    <div class="mx-auto max-w-2xl text-center">
        <x-heading.main>
            {{ $title }}
        </x-heading.main>
        <flux:text size="lg" class="mt-8 font-medium text-pretty sm:text-xl/8">
            {{ $intro }}
        </flux:text>
    </div>
</x-section>
