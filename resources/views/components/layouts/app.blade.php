@props([
    'title' => null,
    'description' => '',
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        {{-- Page SEO (title, description, OG, Twitter) --}}
        <x-layouts.seo :title="$title" :description="$description" :image="$image ?? asset('images/logo.webp')" />

        {{-- JSON-LD Structured Data --}}
        @stack('structured-data')
        @stack('head')

        <!-- Fonts -->
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        @fluxAppearance

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>
    <body class="bg-white">
        <x-layouts.header />

        <main class="bg-white dark:bg-zinc-900">
            {{ $slot }}
        </main>

        @include('partials.footer')

        @livewireScripts
        @fluxScripts

        <script>
            (function () {
                const header = document.getElementById('site-header');
                if (!header) return;

                const setVar = () => {
                    const h = Math.ceil(header.getBoundingClientRect().height);
                    document.documentElement.style.setProperty('--header-h', h + 'px');
                };

                // Initial + on load
                setVar();
                window.addEventListener('load', setVar);
                window.addEventListener('resize', setVar);

                // Keep in sync if header content/line-wrap changes
                const ro = new ResizeObserver(setVar);
                ro.observe(header);
            })();
        </script>
    </body>
</html>
