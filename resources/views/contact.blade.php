<x-layouts.app
    title="Contact"
    description="Find out how to contact us for professional assistance with your property needs."
>
    <x-page-hero title="Contact" />

    <div class="bg-white py-8 sm:py-16">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl divide-y divide-gray-100 lg:mx-0 lg:max-w-none">
                <div class="grid grid-cols-1 gap-10 py-16 lg:grid-cols-3">
                    <div>
                        <flux:heading level="2" size="xl">Get in touch</flux:heading>
                        <flux:text class="mt-4 text-zinc-600">
                            The best way to contact us is either by email or phone.
                        </flux:text>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-2 lg:gap-8">
                        @foreach ([
                                ['title' => 'Lettings', 'email' => 'lettings@steve-morris.co.uk', 'phone' => '+44 (121) 355 0880'],
                                ['title' => 'Sales', 'email' => 'sales@steve-morris.co.uk', 'phone' => '+44 (121) 355 0880'],
                                ['title' => 'Block Management', 'email' => 'block@steve-morris.co.uk', 'phone' => '+44 (121) 355 0880'],
                                ['title' => 'General Enquiries', 'email' => 'contact@steve-morris.co.uk', 'phone' => '+44 (121) 355 0880']
                            ]
                            as $card)
                            <x-card :title="$card['title']">
                                <x-slot:body>
                                    <flux:text size="sm" class="mt-3">
                                        <flux:link href="mailto:{{ $card['email'] }}">{{ $card['email'] }}</flux:link>
                                    </flux:text>
                                    <flux:text size="sm" class="mt-3">{{ $card['phone'] }}</flux:text>
                                </x-slot>
                            </x-card>
                        @endforeach
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-10 py-16 lg:grid-cols-3">
                    <div>
                        <flux:heading level="2" size="xl">Locations</flux:heading>
                        <flux:text class="mt-4 text-zinc-600">Where you can visit us.</flux:text>
                    </div>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-2 lg:gap-8">
                        @foreach ([['title' => 'Sutton Coldfield', 'address' => '1 Coleshill Street, Sutton Coldfield, West Midlands, B72 1SD']]
                            as $card)
                            <x-card :title="$card['title']" :body="$card['address']" />
                        @endforeach

                        <flux:text class="col-span-2 mt-4 italic">
                            Please contact us before you plan on visiting so that we can make sure someone is available
                            to meet you.
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
