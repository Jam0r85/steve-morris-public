@props([
    'title' => null,
    'description' => '',
    '',
])

@php
    $pageTitle = $title ?? null ? $title . ' | ' . config('app.name') : config('app.name');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

        <title>{{ $pageTitle }}</title>

        <meta name="description" content="{{ $description }}" />
        <meta name="author" content="{{ config('app.name') }}" />

        <meta name="robots" content="index, follow" />
        <meta name="theme-color" content="#ffffff" />
        <link rel="canonical" href="{{ url()->current() }}" />

        <link rel="icon" href="/favicon.ico" sizes="any" />
        <link rel="apple-touch-icon" href="/apple-touch-icon.png" />

        <!-- Open Graph for Facebook and others -->
        <meta property="og:title" content="{{ $pageTitle }}" />
        <meta property="og:description" content="{{ $description }}" />
        <meta property="og:image" content="{{ $image ?? asset('images/logo.webp') }}" />
        <meta property="og:url" content="https://example.com/" />
        <meta property="og:type" content="website" />

        <!-- Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ $pageTitle }}" />
        <meta name="twitter:description" content="{{ $description }}" />
        <meta name="twitter:image" content="{{ $image ?? asset('images/logo.webp') }}" />

        <!-- Fonts -->
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxAppearance
    </head>
    <body class="bg-white">
        <header
            x-data="{ open: false }"
            x-on:keydown.escape.window="open=false"
            x-effect="document.body.classList.toggle('overflow-hidden', open)"
        >
            <nav
                class="mx-auto flex max-w-7xl items-start justify-between p-6 lg:items-center lg:px-8"
                aria-label="Global"
            >
                <div class="flex flex-1 items-start lg:items-center">
                    <flux:navbar class="hidden space-x-4 lg:flex">
                        <flux:dropdown>
                            <flux:navbar.item icon:trailing="chevron-down">Properties</flux:navbar.item>
                            <flux:navmenu>
                                <flux:navmenu.item href="{{ route('properties.lettings') }}">To Let</flux:navmenu.item>
                                <flux:navmenu.item href="{{ route('properties.sales') }}">For Sale</flux:navmenu.item>
                            </flux:navmenu>
                        </flux:dropdown>
                        <flux:navbar.item href="{{ route('lettings') }}" title="Lettings">Lettings</flux:navbar.item>
                        <flux:navbar.item href="{{ route('contact') }}" title="Contact">Contact</flux:navbar.item>
                    </flux:navbar>

                    {{-- Mobile open button (top-aligned) --}}
                    <div class="self-start lg:hidden">
                        <button
                            type="button"
                            class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-zinc-700"
                            @click="open = true"
                            :aria-expanded="open.toString()"
                            aria-controls="mobile-menu-panel"
                        >
                            <span class="sr-only">Open main menu</span>
                            <svg
                                class="size-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                aria-hidden="true"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <a href="{{ url('/') }}" title="{{ config('app.name') }}" class="-m-1.5 p-1.5">
                        <span class="sr-only">{{ config('app.name') }}</span>
                        <img
                            class="h-16 w-auto"
                            src="{{ asset('images/logo.webp') }}"
                            alt="{{ config('app.name') }}"
                        />
                    </a>
                </div>

                <div class="hidden flex-1 justify-end lg:flex">
                    <flux:link href="{{ route('valuation') }}" title="Book Valuation" external>
                        <flux:button variant="primary" icon:trailing="arrow-up-right">Book Valuation</flux:button>
                    </flux:link>
                </div>
            </nav>

            <!-- Mobile menu, show/hide based on menu open state. -->
            <div class="lg:hidden" role="dialog" aria-modal="true" x-cloak x-show="open">
                <!-- Background backdrop -->
                <div class="fixed inset-0 z-40 bg-black/50" @click="open=false" x-transition.opacity></div>

                <!-- Slide-over panel -->
                <div
                    id="mobile-menu-panel"
                    class="fixed inset-y-0 left-0 z-50 w-full transform overflow-y-auto bg-white px-6 py-6 sm:max-w-sm"
                    x-transition:enter="transition duration-200 ease-out"
                    x-transition:enter-start="-translate-x-full opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition duration-150 ease-in"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="-translate-x-full opacity-0"
                    tabindex="-1"
                    x-trap.noscroll.inert="open"
                >
                    <div class="flex items-start justify-between">
                        <div class="flex flex-1">
                            <button type="button" class="-m-2.5 rounded-md p-2.5 text-zinc-700" @click="open=false">
                                <span class="sr-only">Close menu</span>
                                <svg
                                    class="size-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <a href="{{ url('/') }}" class="-m-1.5 p-1.5" @click="open=false">
                            <span class="sr-only">
                                {{ config('app.name') }}
                            </span>
                            <img
                                class="h-8 w-auto"
                                src="{{ asset('images/logo.webp') }}"
                                alt="{{ config('app.name') }}"
                            />
                        </a>

                        <div class="flex flex-1 justify-end">
                            <a
                                href="{{ route('valuation') }}"
                                class="text-sm/6 font-semibold text-zinc-900"
                                @click="open=false"
                            >
                                Book Valuation
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>

                    <nav class="mt-6 space-y-2" aria-label="Mobile">
                        <div>
                            <p class="px-3 py-2 text-xs font-semibold text-zinc-500 uppercase">Properties</p>
                            <a
                                href="{{ route('properties.lettings') }}"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-zinc-900 hover:bg-zinc-50"
                                @click="open=false"
                            >
                                To Let
                            </a>
                            <a
                                href="{{ route('properties.sales') }}"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-zinc-900 hover:bg-zinc-50"
                                @click="open=false"
                            >
                                For Sale
                            </a>
                        </div>

                        <a
                            href="{{ route('lettings') }}"
                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-zinc-900 hover:bg-zinc-50"
                            @click="open=false"
                        >
                            Lettings
                        </a>

                        <a
                            href="{{ route('contact') }}"
                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-zinc-900 hover:bg-zinc-50"
                            @click="open=false"
                        >
                            Contact
                        </a>

                        <div class="pt-4">
                            <flux:link href="{{ route('valuation') }}" external>
                                <flux:button
                                    class="w-full"
                                    variant="primary"
                                    icon:trailing="arrow-up-right"
                                    @click="open=false"
                                >
                                    Book Valuation
                                </flux:button>
                            </flux:link>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <main>
            {{ $slot }}
        </main>

        @include('partials.footer')

        @livewireScripts
        @fluxScripts
    </body>
</html>
