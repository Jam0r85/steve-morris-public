<x-layouts.app
    title="Careers"
    description="Join the family-run team at Steve Morris & Son in Sutton Coldfield. Explore estate agency careers in sales, lettings and property management. Apply online today."
>
    <x-page-hero
        title="Work with a family-run team that puts people first"
        intro="We’ve served Sutton Coldfield for over 40 years with a personal, common‑sense approach to sales, lettings and property management. Come grow your career with us."
    />

    <section id="benefits" class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <h2 class="text-2xl font-semibold sm:text-4xl">Why work with us</h2>
                <p class="mt-3 text-zinc-600">
                    Clear expectations, friendly culture, and tools that let you do your best work.
                </p>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                        ['title' => 'Hands‑on training', 'body' => 'Structured onboarding and mentoring from senior staff.'],
                        ['title' => 'Local expertise', 'body' => 'Work in the heart of Sutton Coldfield with a respected local brand.'],
                        ['title' => 'Fair rewards', 'body' => 'Competitive pay with performance bonuses where applicable.'],
                        ['title' => 'Modern systems', 'body' => 'Street.co.uk portals, digital applications and paperless workflows.'],
                        ['title' => 'Community impact', 'body' => 'Help landlords and tenants with a service we’re proud of.'],
                        ['title' => 'Family ethos', 'body' => 'A friendly team that values common sense and integrity.']
                    ]
                    as $card)
                    <x-card :title="$card['title']" :body="$card['body']" />
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.app>
