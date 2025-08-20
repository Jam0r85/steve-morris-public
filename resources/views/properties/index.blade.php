<x-layouts.app
    title="{{ $channel === 'lettings' ? 'Properties to Let' : 'Properties for Sale' }}"
    description=""
>
        <livewire:properties.search :channel="$channel" />           
</x-layouts.app>
