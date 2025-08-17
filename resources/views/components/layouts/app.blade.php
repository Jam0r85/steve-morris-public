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
        <header>
            <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
                <div class="flex flex-1">
                    <flux:navbar class="space-x-4">
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
                    <div class="flex lg:hidden">
                        <button
                            type="button"
                            class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                        >
                            <span class="sr-only">Open main menu</span>
                            <svg
                                class="size-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                aria-hidden="true"
                                data-slot="icon"
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
                    <span class="sr-only">Your Company</span>
                    <a href="{{ url('/') }}" title="{{ config('app.name') }}" class="-m-1.5 p-1.5">
                        <span class="sr-only">Your Company</span>
                        <img
                            class="h-16 w-auto"
                            src="{{ asset('images/logo.webp') }}"
                            alt="{{ config('app.name') }}"
                        />
                    </a>
                </div>
                <div class="flex flex-1 justify-end">
                    <flux:link href="{{ route('valuation') }}" title="Book Valuation" external>
                        <flux:button variant="primary" icon:trailing="arrow-up-right">Book Valuation</flux:button>
                    </flux:link>
                </div>
            </nav>
            <!-- Mobile menu, show/hide based on menu open state. -->
            <div class="lg:hidden" role="dialog" aria-modal="true">
                <!-- Background backdrop, show/hide based on slide-over state. -->
                <div class="fixed inset-0 z-10"></div>
                <div class="fixed inset-y-0 left-0 z-10 w-full overflow-y-auto bg-white px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-1">
                            <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700">
                                <span class="sr-only">Close menu</span>
                                <svg
                                    class="size-6"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                    data-slot="icon"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <a href="#" class="-m-1.5 p-1.5">
                            <span class="sr-only">Your Company</span>
                            <img
                                class="h-8 w-auto"
                                src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600"
                                alt=""
                            />
                        </a>
                        <div class="flex flex-1 justify-end">
                            <a href="#" class="text-sm/6 font-semibold text-gray-900">
                                Log in
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                    <div class="mt-6 space-y-2">
                        <a
                            href="#"
                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50"
                        >
                            Product
                        </a>
                        <a
                            href="#"
                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50"
                        >
                            Features
                        </a>
                        <a
                            href="#"
                            class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50"
                        >
                            Company
                        </a>
                    </div>
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
