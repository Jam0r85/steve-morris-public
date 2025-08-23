<x-section width="container">
    <x-slot name="heading">
        <x-heading.main>Information for Tenants</x-heading.main>
    </x-slot>

    <x-slot name="content">
        <flux:text size="lg" class="mt-4">
            Everything tenants need to know: booking viewings, registering interest, referencing, permitted payments,
            legal checks, and maintenance support.
        </flux:text>

        {{-- At a glance (Feature cards) --}}
        <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            <x-feature-card
                icon="currency-pound"
                header="Upfront costs"
                text="Holding deposit (1 week’s rent), first month’s rent, and a security deposit (usually 5 weeks)."
            />

            <x-feature-card
                icon="identification"
                header="Documents we’ll need"
                text="Photo ID & Right to Rent, basic income details, and referencing info for each applicant aged 18+."
            />

            <x-feature-card
                icon="clock"
                header="Typical timelines"
                text="Referencing usually completes within a few working days once all details are submitted."
            />

            <x-feature-card
                icon="check-circle"
                header="How to apply"
                text="You must have attended a viewing. Then email your details to start referencing with Canopy."
            />

            <x-feature-card
                icon="wrench-screwdriver"
                header="Maintenance & emergencies"
                text="Report repairs by email or Street. Emergencies (e.g. major leaks/no heating) are prioritised."
            />

            <x-feature-card
                icon="shield-check"
                header="Compliance"
                text="Deposits protected (MyDeposits). Fees per the Tenant Fees Act. Right to Rent checks required."
            />
        </div>
    </x-slot>
</x-section>
