{{-- ✅ Skip link for keyboard users --}}
<a
    href="#main-content"
    class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:rounded-md focus:bg-zinc-900 focus:px-3 focus:py-2 focus:text-white dark:focus:bg-white dark:focus:text-zinc-900"
>
    Skip to content
</a>

<header
    id="site-header"
    class="sticky top-0 z-30 border-b border-transparent bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 dark:bg-zinc-900/80"
    x-data="{
        open: null, // desktop dropdown id: 'properties' | 'lettings' | null
        mobileOpen: false, // mobile menu state
        toggle(name) {
            this.open = this.open === name ? null : name
        },
        closeAll() {
            this.open = null
        },
    }"
    x-on:keydown.escape.window="
        closeAll()
        mobileOpen = false
    "
>
    <nav aria-label="Main" class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8">
        {{-- LEFT: Logo --}}
        <div class="flex flex-shrink-0 items-center">
            <a
                href="{{ url('/') }}"
                title="{{ config('app.name') }} - Home"
                aria-label="Go to homepage"
                rel="home"
                class="-m-1.5 rounded-md p-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:focus-visible:ring-zinc-600"
            >
                <img
                    class="h-12 w-auto dark:hidden"
                    src="{{ asset('images/logo.webp') }}"
                    alt="{{ config('app.name') }} Logo"
                />
                <img
                    class="hidden h-12 w-auto dark:block"
                    src="{{ asset('images/logo-dark.webp') }}"
                    alt="{{ config('app.name') }} Logo"
                />
            </a>
        </div>

        {{-- RIGHT: Desktop nav + Mobile toggle --}}
        <div class="flex items-center gap-x-4">
            {{-- Desktop nav (Alpine accordion) --}}
            <ul class="hidden lg:flex lg:items-center lg:gap-x-5" role="menubar" x-on:click.outside="closeAll()">
                {{-- Properties --}}
                <li role="none" class="relative">
                    <details class="group" x-bind:open="open === 'properties'">
                        <summary
                            role="menuitem"
                            class="flex cursor-pointer list-none items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-semibold text-zinc-900/90 hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:text-zinc-100/90 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-haspopup="true"
                            :aria-expanded="open === 'properties'"
                            @click.prevent="toggle('properties')"
                        >
                            Properties
                            <svg
                                class="size-4 transition-transform group-open:rotate-180"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                />
                            </svg>
                        </summary>
                        <ul
                            class="absolute top-full left-0 z-40 mt-2 w-48 rounded-xl border border-zinc-200/70 bg-white/95 p-1 shadow-lg ring-1 ring-zinc-900/5 backdrop-blur dark:border-zinc-700/60 dark:bg-zinc-900/95 dark:ring-white/10"
                            role="menu"
                        >
                            <li role="none">
                                <a
                                    href="{{ route('properties.lettings') }}"
                                    title="View properties to let"
                                    role="menuitem"
                                    class="block rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                                    aria-current="{{ request()->routeIs('properties.lettings') ? 'page' : 'false' }}"
                                    @click="closeAll()"
                                >
                                    To Let
                                </a>
                            </li>
                            <li role="none">
                                <a
                                    href="{{ route('properties.sales') }}"
                                    title="View properties for sale"
                                    role="menuitem"
                                    class="block rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                                    aria-current="{{ request()->routeIs('properties.sales') ? 'page' : 'false' }}"
                                    @click="closeAll()"
                                >
                                    For Sale
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>

                {{-- Lettings --}}
                <li role="none" class="relative">
                    <details class="group" x-bind:open="open === 'lettings'">
                        <summary
                            role="menuitem"
                            class="flex cursor-pointer list-none items-center gap-1.5 rounded-lg px-2.5 py-2 text-sm font-semibold text-zinc-900/90 hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:text-zinc-100/90 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-haspopup="true"
                            :aria-expanded="open === 'lettings'"
                            @click.prevent="toggle('lettings')"
                        >
                            Lettings
                            <svg
                                class="size-4 transition-transform group-open:rotate-180"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z"
                                />
                            </svg>
                        </summary>
                        <ul
                            class="absolute top-full left-0 z-40 mt-2 w-56 rounded-xl border border-zinc-200/70 bg-white/95 p-1 shadow-lg ring-1 ring-zinc-900/5 backdrop-blur dark:border-zinc-700/60 dark:bg-zinc-900/95 dark:ring-white/10"
                            role="menu"
                        >
                            <li role="none">
                                <a
                                    href="{{ route('landlords') }}"
                                    title="Information for landlords"
                                    role="menuitem"
                                    class="block rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                                    aria-current="{{ request()->routeIs('landlords') ? 'page' : 'false' }}"
                                    @click="closeAll()"
                                >
                                    Landlords
                                </a>
                            </li>
                            <li role="none">
                                <a
                                    href="{{ route('tenants') }}"
                                    title="Information for tenants"
                                    role="menuitem"
                                    class="block rounded-lg px-3 py-2 text-sm font-medium hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                                    aria-current="{{ request()->routeIs('tenants') ? 'page' : 'false' }}"
                                    @click="closeAll()"
                                >
                                    Tenants
                                </a>
                            </li>
                        </ul>
                    </details>
                </li>

                {{-- Contact --}}
                <li role="none">
                    <a
                        href="{{ route('contact') }}"
                        title="Contact us"
                        role="menuitem"
                        class="rounded-lg px-2.5 py-2 text-sm font-semibold text-zinc-900/90 hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:text-zinc-100/90 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                        aria-current="{{ request()->routeIs('contact') ? 'page' : 'false' }}"
                        @click="closeAll()"
                    >
                        Contact
                    </a>
                </li>

                {{-- CTA --}}
                <li role="none">
                    <a
                        href="{{ route('valuation') }}"
                        title="Book a property valuation"
                        role="menuitem"
                        class="ml-2 inline-flex items-center gap-2 rounded-full bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100 dark:focus-visible:ring-zinc-600"
                        aria-current="{{ request()->routeIs('valuation') ? 'page' : 'false' }}"
                        @click="closeAll()"
                    >
                        Book Valuation
                        <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M3 10a1 1 0 011-1h9.586L10.293 5.707a1 1 0 111.414-1.414l5.5 5.5a1 1 0 010 1.414l-5.5 5.5A1 1 0 0110.293 15.293L13.586 12H4a1 1 0 01-1-1z"
                            />
                        </svg>
                    </a>
                </li>
            </ul>

            {{-- Mobile burger --}}
            <button
                type="button"
                class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-zinc-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 lg:hidden dark:text-zinc-200 dark:focus-visible:ring-zinc-600"
                aria-label="Open mobile menu"
                aria-controls="mobile-menu"
                :aria-expanded="mobileOpen"
                @click="mobileOpen = true"
            >
                <svg
                    class="size-6"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="1.5"
                    aria-hidden="true"
                >
                    <path
                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </button>
        </div>
    </nav>

    {{-- Mobile menu (dialog) --}}
    <el-dialog>
        <dialog
            id="mobile-menu"
            class="backdrop:bg-zinc-900/40 backdrop:backdrop-blur lg:hidden"
            role="dialog"
            aria-modal="true"
            aria-label="Mobile menu"
            aria-labelledby="mobile-menu-heading"
            x-bind:open="mobileOpen"
            x-on:click.outside="mobileOpen=false"
        >
            <div tabindex="0" class="fixed inset-0 focus:outline-none">
                <el-dialog-panel
                    class="fixed inset-x-0 top-0 z-50 w-full max-w-none border-b border-zinc-200/60 bg-white/95 p-6 shadow-xl ring-1 ring-zinc-900/10 backdrop-blur dark:border-zinc-700/60 dark:bg-zinc-900/95 dark:ring-zinc-100/10"
                >
                    <div class="flex items-center justify-between">
                        <h2 id="mobile-menu-heading" class="sr-only">Main navigation</h2>
                        <a
                            href="{{ url('/') }}"
                            aria-label="Go to homepage"
                            class="-m-1.5 rounded-md p-1.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:focus-visible:ring-zinc-600"
                            @click="mobileOpen=false"
                        >
                            <img
                                class="h-8 w-auto dark:hidden"
                                src="{{ asset('images/logo.webp') }}"
                                alt="{{ config('app.name') }} Logo"
                            />
                            <img
                                class="hidden h-8 w-auto dark:block"
                                src="{{ asset('images/logo-dark.webp') }}"
                                alt="{{ config('app.name') }} Logo"
                            />
                        </a>
                        <button
                            type="button"
                            class="-m-2.5 rounded-md p-2.5 text-zinc-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:text-zinc-300 dark:focus-visible:ring-zinc-600"
                            aria-label="Close mobile menu"
                            aria-controls="mobile-menu"
                            :aria-expanded="mobileOpen"
                            @click="mobileOpen=false"
                        >
                            <svg
                                class="size-6"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                aria-hidden="true"
                            >
                                <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>

                    <nav class="mt-6 space-y-2" aria-label="Mobile">
                        <a
                            href="{{ route('properties.lettings') }}"
                            title="View properties to let"
                            class="block rounded-lg px-3 py-2 text-base font-semibold hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-current="{{ request()->routeIs('properties.lettings') ? 'page' : 'false' }}"
                            @click="mobileOpen=false"
                        >
                            To Let
                        </a>
                        <a
                            href="{{ route('properties.sales') }}"
                            title="View properties for sale"
                            class="block rounded-lg px-3 py-2 text-base font-semibold hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-current="{{ request()->routeIs('properties.sales') ? 'page' : 'false' }}"
                            @click="mobileOpen=false"
                        >
                            For Sale
                        </a>
                        <a
                            href="{{ route('landlords') }}"
                            title="Information for landlords"
                            class="block rounded-lg px-3 py-2 text-base font-semibold hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-current="{{ request()->routeIs('landlords') ? 'page' : 'false' }}"
                            @click="mobileOpen=false"
                        >
                            Landlords
                        </a>
                        <a
                            href="{{ route('tenants') }}"
                            title="Information for tenants"
                            class="block rounded-lg px-3 py-2 text-base font-semibold hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-current="{{ request()->routeIs('tenants') ? 'page' : 'false' }}"
                            @click="mobileOpen=false"
                        >
                            Tenants
                        </a>
                        <a
                            href="{{ route('contact') }}"
                            title="Contact us"
                            class="block rounded-lg px-3 py-2 text-base font-semibold hover:bg-zinc-100/70 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:hover:bg-white/5 dark:focus-visible:ring-zinc-600"
                            aria-current="{{ request()->routeIs('contact') ? 'page' : 'false' }}"
                            @click="mobileOpen=false"
                        >
                            Contact
                        </a>
                    </nav>

                    <div class="mt-6">
                        <a
                            href="{{ route('valuation') }}"
                            title="Book a property valuation"
                            class="inline-flex w-full items-center justify-center gap-2 rounded-full bg-zinc-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-zinc-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-zinc-400 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-100 dark:focus-visible:ring-zinc-600"
                            aria-current="{{ request()->routeIs('valuation') ? 'page' : 'false' }}"
                            @click="mobileOpen=false"
                        >
                            Book Valuation
                            <svg class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M3 10a1 1 0 011-1h9.586L10.293 5.707a1 1 0 111.414-1.414l5.5 5.5a1 1 0 010 1.414l-5.5 5.5A1 1 0 0110.293 15.293L13.586 12H4a1 1 0 01-1-1z"
                                />
                            </svg>
                        </a>
                    </div>
                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
</header>
