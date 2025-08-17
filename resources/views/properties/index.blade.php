<x-layouts.app
    title="{{ $channel === 'lettings' ? 'Properties to Let' : 'Properties for Sale' }}"
    description=""
>
    <x-page-hero
        title="{{ $channel === 'lettings' ? 'Properties to Let' : 'Properties for Sale' }}"
    />

    <section id="properties" class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:properties.search :channel="$channel" />           
        </div>
    </section>
</x-layouts.app>
