<x-layouts.app
    title="Contact"
    description="Get in touch with Steve Morris & Son for lettings, sales, block management and general property enquiries. Phone, email and branch locations."
    :canonical="$canonical"
>
    <x-section class="mt-8">
        <x-slot:heading>
            <x-heading.main id="contact-h1">How to contact us</x-heading.main>
        </x-slot>

        <x-slot:content>
            <div class="mx-auto max-w-7xl divide-y divide-zinc-200">
                {{-- Contact methods --}}
                <div class="grid grid-cols-1 gap-10 py-16 lg:grid-cols-3" aria-labelledby="get-in-touch">
                    <div>
                        <flux:heading level="3" size="xl" id="get-in-touch">Get in touch</flux:heading>
                        <flux:text size="lg" class="mt-4">
                            The best way to contact us is either by email or phone. We usually reply within 1 business
                            day.
                        </flux:text>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-2 lg:gap-8">
                        @foreach ($departments as $dept)
                            <x-card :title="$dept['title']">
                                <x-slot:body>
                                    <flux:text class="mt-3">
                                        <flux:link
                                            href="mailto:{{ $dept['email'] }}"
                                            title="Email {{ $dept['title'] }}"
                                            aria-label="Email {{ $dept['title'] }} at {{ $dept['email'] }}"
                                        >
                                            {{ $dept['email'] }}
                                        </flux:link>
                                    </flux:text>

                                    @php
                                        $telHref = 'tel:' . preg_replace('/[^\d+]/', '', $dept['phone']);
                                    @endphp

                                    <flux:text class="mt-3">
                                        <flux:link
                                            href="{{ $telHref }}"
                                            title="Call {{ $dept['title'] }}"
                                            aria-label="Call {{ $dept['title'] }} on {{ $dept['phone'] }}"
                                        >
                                            {{ $dept['phone'] }}
                                        </flux:link>
                                    </flux:text>
                                </x-slot>
                            </x-card>
                        @endforeach
                    </div>
                </div>

                {{-- Locations --}}
                <div class="grid grid-cols-1 gap-10 py-16 lg:grid-cols-3" aria-labelledby="locations">
                    <div>
                        <flux:heading level="3" size="xl" id="locations">Locations</flux:heading>
                        <flux:text size="lg" class="mt-4">Where you can visit us.</flux:text>

                        <flux:callout color="amber" class="mt-6">
                            <flux:callout.heading icon="newspaper">Before you visit</flux:callout.heading>
                            <flux:callout.text>
                                Please contact us before you plan on visiting so that we can make sure someone is
                                available to meet you.
                            </flux:callout.text>
                        </flux:callout>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-2 lg:gap-8">
                        @foreach ($branches as $branch)
                            <x-card :title="$branch->display_name">
                                <x-slot:body>
                                    <flux:text class="mt-1">{{ $branch->address_public }}</flux:text>

                                    <div class="mt-4 space-y-2">
                                        @if ($branch->phone)
                                            <div>
                                                <flux:link
                                                    href="{{ $branch->tel_href }}"
                                                    title="Call {{ $branch->display_name }}"
                                                    aria-label="Call {{ $branch->display_name }} on {{ $branch->phone }}"
                                                >
                                                    {{ $branch->phone }}
                                                </flux:link>
                                            </div>
                                        @endif

                                        @if ($branch->email)
                                            <div>
                                                <flux:link
                                                    href="mailto:{{ $branch->email }}"
                                                    title="Email {{ $branch->display_name }}"
                                                    aria-label="Email {{ $branch->display_name }} at {{ $branch->email }}"
                                                >
                                                    {{ $branch->email }}
                                                </flux:link>
                                            </div>
                                        @endif

                                        @if ($branch->website_url)
                                            <div>
                                                <flux:link
                                                    href="{{ $branch->website_url }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Visit {{ $branch->display_name }} website"
                                                    aria-label="Visit {{ $branch->display_name }} website"
                                                >
                                                    Website
                                                </flux:link>
                                            </div>
                                        @endif

                                        @if ($branch->maps_href)
                                            <div>
                                                <flux:link
                                                    href="{{ $branch->maps_href }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                    title="Directions to {{ $branch->display_name }}"
                                                    aria-label="Get directions to {{ $branch->display_name }}"
                                                >
                                                    Get directions
                                                </flux:link>
                                            </div>
                                        @endif
                                    </div>
                                </x-slot>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-slot>
    </x-section>

    @push('structured-data')
        {!! $jsonLdScript !!}
    @endpush
</x-layouts.app>
