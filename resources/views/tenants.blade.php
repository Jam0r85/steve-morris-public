<x-layouts.app :title="$title" :description="$description">
    <div class="bg-zinc-100 dark:bg-zinc-800">
        <x-tenants.overview />
    </div>

    <x-separator />

    <div class="bg-white dark:bg-zinc-900">
        <x-tenants.fee-schedule />
    </div>

    <x-separator />

    <div class="bg-zinc-100 dark:bg-zinc-800">
        <x-tenants.how-to-apply
            email="applications@steve-morris.co.uk"
            partnerName="Canopy"
            partnerUrl="https://www.canopy.rent/"
        />
    </div>

    <x-separator />

    <div class="bg-white dark:bg-zinc-900">
        <x-section width="container">
            <x-heading.sub>Frequently Asked Questions</x-heading.sub>

            <div class="mt-16">
                <x-faq.accordion :items="$tenantFaqs" />
            </div>
        </x-section>

        <x-cta.dark-card
            title="Ready to rent?"
            copy="Search available properties or speak to our team about your application."
            primary-label="See properties"
            :primary-href="route('properties.lettings')"
            secondary-label="Get in touch"
            :secondary-href="route('contact')"
        />
    </div>

    @push('structured-data')
        {!! $jsonLdScript !!}
    @endpush
</x-layouts.app>
