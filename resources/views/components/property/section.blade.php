@props(['id', 'title'])

<section id="{{  $id }}" class="py-8">
    <x-flux::heading level="2" size="xl" class="mb-4">
        {{  $title }}
    </x-flux::heading>

    {{  $slot }}
</section>