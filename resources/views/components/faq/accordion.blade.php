@props([
    // Array of JSON-LD Question objects:
    // ['@type'=>'Question','name'=>'...','acceptedAnswer'=>['@type'=>'Answer','text'=>'...']]
    'items' => [],
])

<div>
    <flux:accordion transition>
        @foreach ($items as $item)
            @php
                $q = $item['name'] ?? '';
                $a = $item['acceptedAnswer']['text'] ?? '';
            @endphp

            <flux:accordion.item>
                <flux:accordion.heading>
                    <span class="text-base/7 font-semibold">
                        {{ $q }}
                    </span>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <span class="text-base/7">
                        {{ $a }}
                    </span>
                </flux:accordion.content>
            </flux:accordion.item>
        @endforeach
    </flux:accordion>
</div>
