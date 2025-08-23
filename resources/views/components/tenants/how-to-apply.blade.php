@props([
    'email' => 'lettings@stevemorris.co.uk',
    'partnerName' => 'Canopy',
    'partnerUrl' => 'https://www.canopy.rent/',
])

<x-section id="how-to-apply" width="container">
    <x-slot:heading>
        <x-heading.sub level="2">Our application process</x-heading.sub>
    </x-slot>

    <x-slot:content>
        <flux:text size="lg" class="mt-6">
            To start your application, please email us the following details for
            <strong>every applicant aged 18 or over</strong>
            :
        </flux:text>

        <ul class="mt-4 list-disc space-y-1 pl-6">
            <li><flux:text size="lg">Full name, email address, and telephone number</flux:text></li>
            <li><flux:text size="lg">Basic income information (employment or other)</flux:text></li>
            <li><flux:text size="lg">Photo ID (passport or driving licence)</flux:text></li>
            <li><flux:text size="lg">Your preferred tenancy start date</flux:text></li>
        </ul>

        <flux:text size="lg" class="mt-4">
            Send to
            <a href="mailto:{{ $email }}" class="font-medium underline underline-offset-4">
                {{ $email }}
            </a>
            . We’ll then pass your details securely to our referencing partner
            <a
                href="{{ $partnerUrl }}"
                target="_blank"
                rel="noopener"
                class="font-medium underline underline-offset-4"
            >
                {{ $partnerName }}
            </a>
            to complete full checks.
        </flux:text>

        <flux:callout color="amber" class="mt-12">
            <flux:callout.heading icon="exclamation-triangle">Before you apply</flux:callout.heading>
            <flux:callout.text>
                You (and any adult co-applicants, where possible) must have
                <strong>viewed the property</strong>
                . We’re unable to progress applications unless a viewing has taken place.
            </flux:callout.text>
        </flux:callout>

        <flux:callout icon="sparkles" color="purple" class="mt-6">
            <flux:callout.heading>Good to know</flux:callout.heading>
            <flux:callout.text>
                <ul class="list-disc space-y-1 pl-5">
                    <li>Providing these details doesn’t reserve or guarantee the property.</li>
                    <li>
                        {{ $partnerName }} will be instructed to carry out a Credit Check, confirm income/employment
                        details and check ID using the information provided.
                    </li>
                    <li>
                        {{ $partnerName }} may request further documents (e.g. payslips, bank statements) directly and
                        securely.
                    </li>
                </ul>
            </flux:callout.text>
        </flux:callout>
    </x-slot>
</x-section>
