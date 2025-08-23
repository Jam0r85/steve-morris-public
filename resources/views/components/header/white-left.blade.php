@props([
    'title',
    'intro',
])

<x-section pad="lg">
    <x-heading.main>
        {{ $title }}
    </x-heading.main>
    <p class="mt-8 text-lg font-medium text-pretty text-zinc-500 sm:text-xl/8 dark:text-zinc-400">
        {{ $intro }}
    </p>
</x-section>
