{{-- resources/views/home.blade.php --}}
<x-layouts.app
    title="Estate & Letting Agents in Sutton Coldfield | Steve Morris & Son"
    description="Family-run estate and letting agents in Sutton Coldfield. Clear advice for selling, letting and renting, with modern systems and a personal approach."
>
    {{-- HERO --}}
    <section class="py-12 sm:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-8 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-7">
                    <flux:heading level="h1" class="text-3xl sm:text-5xl">
                        Letting and Selling in Sutton Coldfield
                    </flux:heading>
                    <flux:text class="mt-4 text-zinc-600">
                        A family-run agency where personal service meets local expertise. Spanning two generations, we
                        use common-sense solutions for sales, lettings and property management.
                    </flux:text>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <flux:link href="{{ route('properties.lettings') }}">
                            <flux:button variant="subtle" icon="home-modern">Browse lettings</flux:button>
                        </flux:link>
                        <flux:link href="{{ route('properties.sales') }}">
                            <flux:button variant="subtle" icon="home-modern">Browse sales</flux:button>
                        </flux:link>
                    </div>
                </div>

                {{-- Optional: small image mosaic (swap sources as you wish) --}}
                <div class="lg:col-span-5">
                    <div class="grid h-64 grid-cols-3 grid-rows-2 gap-2 sm:h-72 md:h-80">
                        <img
                            class="col-span-2 row-span-2 h-full w-full rounded-2xl object-cover"
                            src="{{ asset('images/home/hero-1.jpg') }}"
                            alt="Sutton Coldfield high street"
                        />
                        <img
                            class="h-full w-full rounded-2xl object-cover"
                            src="{{ asset('images/home/hero-2.jpg') }}"
                            alt="Local park"
                        />
                        <img
                            class="h-full w-full rounded-2xl object-cover"
                            src="{{ asset('images/home/hero-3.jpg') }}"
                            alt="Period homes"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- PARTNERS --}}
    <section class="border-y bg-white py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <flux:heading level="h2" class="sr-only">Our partners</flux:heading>
            <div class="grid grid-cols-2 items-center gap-6 opacity-80 sm:grid-cols-3 md:grid-cols-5">
                <img src="{{ asset('images/partners/mydeposits.png') }}" class="mx-auto h-10" alt="MyDeposits" />
                <img src="{{ asset('images/partners/onthemarket.webp') }}" class="mx-auto h-8" alt="OnTheMarket" />
                <img src="{{ asset('images/partners/safeagent.webp') }}" class="mx-auto h-10" alt="safeagent" />
                <img src="{{ asset('images/partners/ukala.webp') }}" class="mx-auto h-10" alt="UKALA" />
                <img src="{{ asset('images/partners/canopy.webp') }}" class="mx-auto h-8" alt="Canopy" />
            </div>
        </div>
    </section>

    {{-- SUPPORTED BY STREET --}}
    <section class="py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
                <div>
                    <flux:heading level="h2" class="sm:text-4xl">Supported by Street</flux:heading>
                    <flux:text class="mt-4 text-zinc-600">
                        World-class estate agency technology with real-time updates for sales and lettings, and a modern
                        experience for landlords and tenants.
                    </flux:text>
                    <ul class="mt-6 space-y-3 text-sm text-zinc-700">
                        <li class="flex gap-2">
                            <span class="mt-1 inline-block size-2 rounded-full bg-emerald-500"></span>
                            Landlord app for clarity on income and maintenance
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 inline-block size-2 rounded-full bg-emerald-500"></span>
                            Sales progression transparency with 24/7 portal access
                        </li>
                        <li class="flex gap-2">
                            <span class="mt-1 inline-block size-2 rounded-full bg-emerald-500"></span>
                            Tenants can access info and report issues any time
                        </li>
                    </ul>
                    <div class="mt-6">
                        <flux:link href="{{ route('contact') }}">
                            <flux:button icon="chat-bubble-left-right">Ask how it helps you</flux:button>
                        </flux:link>
                    </div>
                </div>
                <div>
                    <div class="aspect-[16/10] w-full overflow-hidden rounded-2xl border">
                        <img
                            src="{{ asset('images/home/street-os.png') }}"
                            alt="Street.co.uk dashboard"
                            class="h-full w-full object-cover"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- AUDIENCE CARDS --}}
    <section class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('landlords') }}" class="rounded-2xl border p-6 transition hover:bg-zinc-50">
                    <flux:heading level="h3" class="text-xl">Landlord</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">
                        Clear fees, full management and paperless workflows.
                    </flux:text>
                    <div class="mt-4">
                        <flux:button size="sm" icon="arrow-right" variant="subtle">Learn more</flux:button>
                    </div>
                </a>
                <a href="{{ route('tenants') }}" class="rounded-2xl border p-6 transition hover:bg-zinc-50">
                    <flux:heading level="h3" class="text-xl">Tenant</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">How to apply, permitted payments and support.</flux:text>
                    <div class="mt-4">
                        <flux:button size="sm" icon="arrow-right" variant="subtle">Learn more</flux:button>
                    </div>
                </a>
                <a href="{{ route('properties.sales') }}" class="rounded-2xl border p-6 transition hover:bg-zinc-50">
                    <flux:heading level="h3" class="text-xl">Buying</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">Browse available properties and register interest.</flux:text>
                    <div class="mt-4">
                        <flux:button size="sm" icon="arrow-right" variant="subtle">Search sales</flux:button>
                    </div>
                </a>
                <a href="{{ route('valuation') }}" class="rounded-2xl border p-6 transition hover:bg-zinc-50">
                    <flux:heading level="h3" class="text-xl">Selling</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">
                        Free appraisal, high-quality marketing and guidance.
                    </flux:text>
                    <div class="mt-4">
                        <flux:button size="sm" icon="arrow-right" variant="subtle">Book valuation</flux:button>
                    </div>
                </a>
            </div>
        </div>
    </section>

    {{-- OPTIONAL: FEATURED PROPERTIES (wire up your own component) --}}
    @if (class_exists(\App\Livewire\Properties\Featured::class))
        <section class="py-16 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <flux:heading level="h2" class="sm:text-4xl">Featured properties</flux:heading>
                <flux:text class="mt-3 text-zinc-600">A few highlights from our current instructions.</flux:text>
                <div class="mt-8">
                    <livewire:properties.featured :limit="6" />
                </div>
                <div class="mt-6">
                    <flux:link href="{{ route('properties.lettings') }}">
                        <flux:button variant="subtle" icon="home-modern">See all lettings</flux:button>
                    </flux:link>
                    <span class="mx-2"></span>
                    <flux:link href="{{ route('properties.sales') }}">
                        <flux:button variant="subtle" icon="banknotes">See all sales</flux:button>
                    </flux:link>
                </div>
            </div>
        </section>
    @endif

    {{-- SELLING HIGHLIGHTS --}}
    <section class="bg-white py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl">
                <flux:heading level="h2" class="sm:text-4xl">Selling with a family-run team</flux:heading>
                <flux:text class="mt-3 text-zinc-600">
                    Over 40 years helping local sellers with straightforward, personal advice and attentive service.
                </flux:text>
            </div>

            <div class="mt-10 grid gap-6 md:grid-cols-3">
                <div class="rounded-2xl border p-6">
                    <flux:heading level="h3" class="text-lg">Family-run estate agent</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">
                        Decades of local experience with the service you’d expect from a small, dedicated team.
                    </flux:text>
                </div>
                <div class="rounded-2xl border p-6">
                    <flux:heading level="h3" class="text-lg">Town centre location</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">
                        Easy to reach in Sutton Coldfield, serving nearby areas with strong local knowledge.
                    </flux:text>
                </div>
                <div class="rounded-2xl border p-6">
                    <flux:heading level="h3" class="text-lg">Professional & personal</flux:heading>
                    <flux:text class="mt-2 text-zinc-600">
                        Clear communication, tailored marketing and guidance from listing to completion.
                    </flux:text>
                </div>
            </div>
        </div>
    </section>

    {{--
        =========================
        Home: Testimonials — Masonry
        =========================
    --}}
    @php
        $testimonials = [
            ['name' => 'Anna Brookes', 'quote' => 'Best lettings agents I’ve ever used. The most outstanding service I’ve received in 20 years of renting.', 'avatar' => 'https://ui-avatars.com/api/?background=C33E06&color=31ED72&name=A+B', 'source' => 'Google Review'],
            ['name' => 'Joseph Holland', 'quote' => 'Very positive experience of dealing with Steve. He clearly cares about his customers and was a pleasure to deal with.', 'avatar' => 'https://ui-avatars.com/api/?background=76B52A&color=7BC81F&name=J+H', 'source' => 'Google Review'],
            ['name' => 'Jenni Fryer', 'quote' => 'Steve Morris was recommended by a friend when I became an accidental landlord. They have been brilliant in recruiting tenants, managing checks and ensuring any issues were resolved quickly and efficiently. Their fees are reasonable and the team are always helpful and responsive via phone or email. Would highly recommend.', 'avatar' => 'https://ui-avatars.com/api/?background=6A319F&color=C751DD&name=J+F', 'source' => 'Google Review'],
            ['name' => 'Charlotte Barnett', 'quote' => 'Could not have made our first time landlord experience any easier. Fabulous service. Many thanks Steve and team', 'avatar' => 'https://ui-avatars.com/api/?background=D01ABA&color=34BADB&name=C+B', 'source' => 'Google Review'],
            ['name' => 'Tony Tansley', 'quote' => "Amazing service and professionalism. I wouldn't even consider going with another agents in the area! Thanks Steve and James", 'avatar' => 'https://ui-avatars.com/api/?background=F35D98&color=49C463&name=T+T', 'source' => 'Google Review'],
            ['name' => 'Marta', 'quote' => 'Very helpful and reliable agency. Really kind and polite staff. Helped us to find a perfect property to rent. I can honestly recommend them!', 'avatar' => 'https://ui-avatars.com/api/?background=5C748E&color=4B6E3A&name=M', 'source' => 'Google Review'],
            ['name' => 'Karen Patton', 'quote' => 'So helpful, have tried to deal with a few estate agents over the last few weeks, Steve Morris were the only ones who got straight back to me and have been nothing but helpful since, definitely recommend.', 'avatar' => 'https://ui-avatars.com/api/?background=FB843A&color=79C834&name=K+P', 'source' => 'Google Review'],
        ];
    @endphp

    <div class="relative isolate bg-white pt-24 pb-32 sm:pt-32 dark:bg-gray-900">
        {{-- Gradient blobs --}}
        <div
            aria-hidden="true"
            class="absolute inset-x-0 top-1/2 -z-10 -translate-y-1/2 transform-gpu overflow-hidden opacity-30 blur-3xl"
        >
            <div
                class="ml-[max(50%,38rem)] aspect-[1313/771] w-[82rem] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc]"
                style="
                    clip-path: polygon(
                        74.1% 44.1%,
                        100% 61.6%,
                        97.5% 26.9%,
                        85.5% 0.1%,
                        80.7% 2%,
                        72.5% 32.5%,
                        60.2% 62.4%,
                        52.4% 68.1%,
                        47.5% 58.3%,
                        45.2% 34.5%,
                        27.5% 76.7%,
                        0.1% 64.9%,
                        17.9% 100%,
                        27.6% 76.8%,
                        76.1% 97.7%,
                        74.1% 44.1%
                    );
                "
            ></div>
        </div>
        <div
            aria-hidden="true"
            class="absolute inset-x-0 top-0 -z-10 flex transform-gpu overflow-hidden pt-32 opacity-25 blur-3xl sm:pt-40 xl:justify-end"
        >
            <div
                class="-ml-24 aspect-[1313/771] w-[82rem] flex-none origin-top-right rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] xl:mr-[calc(50%-12rem)] xl:ml-0"
                style="
                    clip-path: polygon(
                        74.1% 44.1%,
                        100% 61.6%,
                        97.5% 26.9%,
                        85.5% 0.1%,
                        80.7% 2%,
                        72.5% 32.5%,
                        60.2% 62.4%,
                        52.4% 68.1%,
                        47.5% 58.3%,
                        45.2% 34.5%,
                        27.5% 76.7%,
                        0.1% 64.9%,
                        17.9% 100%,
                        27.6% 76.8%,
                        76.1% 97.7%,
                        74.1% 44.1%
                    );
                "
            ></div>
        </div>

        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-base/7 font-semibold text-indigo-600 dark:text-indigo-400">Testimonials</h2>
                <p
                    class="mt-2 text-4xl font-semibold tracking-tight text-balance text-gray-900 sm:text-5xl dark:text-white"
                >
                    We’ve worked with hundreds of amazing people
                </p>
            </div>

            {{-- Masonry container: columns + child avoidance --}}
            <div class="mx-auto mt-16 max-w-2xl sm:mt-20 xl:mx-0 xl:max-w-none">
                <div class="columns-1 [column-gap:2rem] sm:columns-2 xl:columns-4">
                    @foreach ($testimonials as $t)
                        <figure
                            class="mb-8 inline-block w-full break-inside-avoid rounded-2xl bg-white p-6 shadow-lg ring-1 ring-gray-900/5 dark:bg-gray-800/75 dark:shadow-none dark:ring-white/10"
                        >
                            <blockquote class="text-gray-900 dark:text-white">
                                <p>“{{ $t['quote'] }}”</p>
                            </blockquote>
                            <figcaption class="mt-6 flex items-center gap-x-4">
                                <img
                                    src="{{ $t['avatar'] }}"
                                    alt="{{ $t['name'] }}"
                                    class="size-10 rounded-full bg-gray-50 dark:bg-gray-700"
                                />
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $t['name'] }}</div>
                                    <div class="text-gray-600 dark:text-gray-400">{{ $t['source'] }}</div>
                                </div>
                            </figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- FINAL CTA --}}
    <section class="bg-white py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="items-center justify-between gap-6 rounded-2xl border p-6 md:flex">
                <div>
                    <flux:heading level="h3" class="text-xl">Ready to move?</flux:heading>
                    <flux:text class="mt-1 text-zinc-600">
                        Book a free appraisal or browse our current properties.
                    </flux:text>
                </div>
                <div class="mt-4 flex gap-3 md:mt-0">
                    <flux:link href="{{ route('valuation') }}" external>
                        <flux:button variant="primary" icon="arrow-up-right">Book valuation</flux:button>
                    </flux:link>
                    <flux:link href="{{ route('properties.lettings') }}">
                        <flux:button variant="subtle" icon="home-modern">See lettings</flux:button>
                    </flux:link>
                </div>
            </div>
        </div>
    </section>

    {{-- JSON-LD (Organization, WebSite, Breadcrumbs) --}}
    @php
        $orgName = config('app.name', 'Steve Morris & Son');
        $logo = asset('images/logo.webp');
        $now = now()->toAtomString();

        $organization = [
            '@context' => 'https://schema.org',
            '@type' => 'RealEstateAgent',
            'name' => $orgName,
            'url' => url('/'),
            'logo' => $logo,
            'image' => $logo,
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '1 Coleshill Street',
                'addressLocality' => 'Sutton Coldfield',
                'addressRegion' => 'West Midlands',
                'postalCode' => 'B72 1SD',
                'addressCountry' => 'GB',
            ],
            'areaServed' => ['Sutton Coldfield', 'Erdington', 'Kingstanding', 'Shenstone', 'Walsall'],
        ];

        $website = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $orgName,
            'url' => url('/'),
            'inLanguage' => 'en-GB',
            'dateModified' => $now,
            'publisher' => ['@type' => 'Organization', 'name' => $orgName, 'url' => url('/')],
        ];

        $breadcrumbs = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => url('/')]],
        ];

        $schemas = [$organization, $website, $breadcrumbs];
    @endphp

    <x-schema.json-ld :data="$schemas" />
</x-layouts.app>
