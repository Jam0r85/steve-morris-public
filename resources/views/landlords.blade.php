<x-layouts.app
    title="Landlords | Lettings & Property Management — Sutton Coldfield"
    description="Clear fees, strong marketing and a full management option that keeps you compliant. Family-run lettings in Sutton Coldfield."
>
    <x-page-hero
        title="Lettings made simple"
        intro="No jargon. Just the personal service you’d expect from a family-run agent of 40+ years — with clear fees and paperless workflows."
    />

    {{-- Why let with us --}}
    <section id="benefits" class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <flux:heading level="h2" class="sm:text-4xl">Why let with us</flux:heading>
                <flux:text class="mt-3 text-zinc-600">
                    Comprehensive service, strong local coverage, and crisp marketing across our site, portals and
                    social.
                </flux:text>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ([
                        [
                            'title' => 'Comprehensive service',
                            'body' => 'Tenant-find and full management options that cover the essentials end-to-end.'
                        ],
                        [
                            'title' => 'Local coverage',
                            'body' => 'Sutton Coldfield, Erdington, Kingstanding, Shenstone and parts of Walsall.'
                        ],
                        ['title' => 'Crisp marketing', 'body' => 'Your property on our website, OnTheMarket, Zoopla and social media.'],
                        [
                            'title' => 'Compliance handled',
                            'body' => 'Right to Rent, deposit protection, prescribed information and notices.'
                        ],
                        [
                            'title' => 'Paperless workflows',
                            'body' => 'Digital applications, references and statements — fast and transparent.'
                        ],
                        ['title' => 'Family ethos', 'body' => 'Friendly, common-sense advice with clear expectations.']
                    ]
                    as $card)
                    <x-card :title="$card['title']" :body="$card['body']" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- Fees CTA (PDF link) --}}
    <section id="fees" class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <flux:heading level="h2" class="sm:text-4xl">Our landlord fees</flux:heading>
                <flux:text class="mt-3 text-zinc-600">
                    We keep pricing simple and transparent. Download our latest charges for full details.
                </flux:text>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <a
                    href="{{ asset('landlord_charges_october_2024.pdf') }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex"
                >
                    <flux:button icon="document-text">Download fees (PDF)</flux:button>
                </a>
                <flux:link href="{{ route('valuation') }}" external>
                    <flux:button variant="primary" icon="arrow-up-right">Book a valuation</flux:button>
                </flux:link>
            </div>

            <flux:text class="mt-4 text-xs text-zinc-500">
                Fees include VAT. Always refer to the PDF for the current schedule.
            </flux:text>
        </div>
    </section>

    {{-- Calculator --}}
    <section id="calculator" class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <flux:heading level="h2" class="sm:text-4xl">What will I receive each month?</flux:heading>
                <flux:text class="mt-3 text-zinc-600">
                    Enter the monthly rent. Toggle rent guarantee if you want 12-month cover added.
                </flux:text>
            </div>

            <div
                x-data="{
                    rent: 1200,
                    guarantee: false,
                    get mgmt() {
                        return this.rent * 0.12
                    },
                    get rg() {
                        return this.guarantee ? this.rent * 0.06 : 0
                    },
                    get net() {
                        return Math.max(0, this.rent - this.mgmt - this.rg)
                    },
                    fmt(v) {
                        return new Intl.NumberFormat('en-GB', {
                            style: 'currency',
                            currency: 'GBP',
                        }).format(v)
                    },
                }"
                class="mt-8 grid gap-6 lg:grid-cols-2"
            >
                <div class="space-y-4">
                    <flux:input
                        type="number"
                        min="0"
                        step="50"
                        label="Monthly rent (pcm)"
                        icon="currency-pound"
                        class="w-full"
                        x-model.number="rent"
                    />
                    <div class="flex items-center gap-3">
                        <flux:checkbox x-model="guarantee">
                            Add rent guarantee &amp; legal cover (6% pcm)
                        </flux:checkbox>
                    </div>
                    <flux:text class="text-xs text-zinc-500">
                        Estimates only. One-off set-up fees are not included here — see the fees PDF.
                    </flux:text>
                </div>

                <div class="rounded-2xl border p-6">
                    <flux:heading level="h3" class="text-xl">Estimate (Fully Managed)</flux:heading>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-zinc-600">Management (12%)</dt>
                            <dd x-text="fmt(mgmt)"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-600">Rent guarantee (6%)</dt>
                            <dd x-text="guarantee ? fmt(rg) : '—'"></dd>
                        </div>
                        <div class="flex justify-between border-t pt-3">
                            <dt class="font-medium">Estimated to you (pcm)</dt>
                            <dd class="font-semibold" x-text="fmt(net)"></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-zinc-600">Estimated to you (per year)</dt>
                            <dd x-text="fmt(net * 12)"></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQs --}}
    <section id="faqs" class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <flux:heading level="h2" class="sm:text-4xl">Landlord FAQs</flux:heading>
                <flux:text class="mt-3 text-zinc-600">Quick answers to common questions.</flux:text>
            </div>

            <div class="mt-10">
                <flux:accordion>
                    <flux:accordion.item>
                        <flux:accordion.heading>Do I need to tell my mortgage provider?</flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:text>
                                Yes — you’ll need consent to let, and lenders may add conditions while the property is
                                rented.
                            </flux:text>
                        </flux:accordion.content>
                    </flux:accordion.item>

                    <flux:accordion.item>
                        <flux:accordion.heading>Why use a managing agent?</flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:text>
                                It saves time and keeps a professional distance — we handle arrears, deposit disputes,
                                notices and repairs.
                            </flux:text>
                        </flux:accordion.content>
                    </flux:accordion.item>

                    <flux:accordion.item>
                        <flux:accordion.heading>Why have an inventory?</flux:accordion.heading>
                        <flux:accordion.content>
                            <flux:text>
                                A photographic inventory records condition at move-in and is vital evidence if there’s a
                                dispute.
                            </flux:text>
                        </flux:accordion.content>
                    </flux:accordion.item>
                </flux:accordion>
            </div>

            <div class="mt-8 flex flex-wrap gap-3">
                <flux:link href="{{ route('contact') }}">
                    <flux:button icon="envelope">Ask us a question</flux:button>
                </flux:link>
                <flux:link href="{{ route('properties.lettings') }}">
                    <flux:button icon="home-modern" variant="subtle">See properties to let</flux:button>
                </flux:link>
            </div>
        </div>
    </section>

    @php
        $org = config('app.name', 'Steve Morris & Son');
        $now = now()->toAtomString();

        $webLd = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => 'Landlords | Lettings & Property Management — Sutton Coldfield',
            'description' => 'Clear fees, strong marketing and a full management option that keeps you compliant.',
            'url' => url()->current(),
            'dateModified' => $now,
            'publisher' => ['@type' => 'Organization', 'name' => $org, 'url' => url('/')],
        ];
        $breadcrumbsLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Lettings', 'item' => route('lettings')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'Landlords', 'item' => route('landlords')],
            ],
        ];
        $faqLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => 'Do I need to tell my mortgage provider?',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes — you’ll need consent to let, and lenders may add conditions while the property is rented.'],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Why use a managing agent?',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'It saves time and keeps a professional distance — we handle arrears, deposit disputes, notices and repairs.'],
                ],
                [
                    '@type' => 'Question',
                    'name' => 'Why have an inventory?',
                    'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A photographic inventory records condition at move-in and is vital evidence if there’s a dispute.'],
                ],
            ],
        ];

        $schemas = [$webLd, $breadcrumbsLd, $faqLd];
    @endphp

    @push('structured-data')
        <x-schema.json-ld :data="$schemas" />
    @endpush
</x-layouts.app>
