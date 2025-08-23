<footer class="bg-zinc-700">
    <div class="mx-auto max-w-7xl overflow-hidden px-6 py-20 sm:py-24 lg:px-8">
        <flux:navbar class="-mb-6 justify-center gap-x-12 gap-y-3 text-sm/6" aria-label="Footer">
            <flux:link
                href="{{ route('about') }}"
                title="About"
                class="text-accent-foreground hover:text-white"
                variant="ghost"
            >
                About
            </flux:link>
            <flux:link
                href="{{ route('valuation') }}"
                title="Arrange Valuation"
                external
                class="text-accent-foreground hover:text-white"
                variant="ghost"
            >
                Arrange Valuation
            </flux:link>
            <flux:link
                href="{{ route('jobs') }}"
                title="Jobs"
                class="text-accent-foreground hover:text-white"
                variant="ghost"
            >
                Careers
            </flux:link>
            <flux:link
                href="{{ route('landlords') }}"
                title="Landlords"
                class="text-accent-foreground hover:text-white"
                variant="ghost"
            >
                Landlords
            </flux:link>
            <flux:link
                href="{{ route('tenants') }}"
                title="Tenants"
                class="text-accent-foreground hover:text-white"
                variant="ghost"
            >
                Tenants
            </flux:link>
            <flux:link
                href="{{ route('contact') }}"
                title="Contact"
                class="text-accent-foreground hover:text-white"
                variant="ghost"
            >
                Contact
            </flux:link>
        </flux:navbar>

        <div class="mt-16 flex justify-center gap-x-10">
            <flux:link
                variant="ghost"
                href="{{ route('facebook') }}"
                external
                class="text-accent-foreground hover:text-white"
            >
                <span class="sr-only">Facebook</span>
                <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        fill-rule="evenodd"
                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                        clip-rule="evenodd"
                    />
                </svg>
            </flux:link>
            <flux:link
                variant="ghost"
                href="{{ route('twitter') }}"
                external
                class="text-accent-foreground hover:text-white"
            >
                <span class="sr-only">X</span>
                <svg class="size-6" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        d="M13.6823 10.6218L20.2391 3H18.6854L12.9921 9.61788L8.44486 3H3.2002L10.0765 13.0074L3.2002 21H4.75404L10.7663 14.0113L15.5685 21H20.8131L13.6819 10.6218H13.6823ZM11.5541 13.0956L10.8574 12.0991L5.31391 4.16971H7.70053L12.1742 10.5689L12.8709 11.5655L18.6861 19.8835H16.2995L11.5541 13.096V13.0956Z"
                    />
                </svg>
            </flux:link>
        </div>
        <flux:text class="mt-10 text-center text-zinc-300">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            <flux:link variant="ghost" href="{{ route('cookie-policy') }}" external class="text-zinc-300">
                Cookies
            </flux:link>
            |
            <flux:link variant="ghost" href="{{ route('privacy-policy') }}" external class="text-zinc-300">
                Privacy Policy
            </flux:link>
            |
            <flux:link variant="ghost" href="{{ route('cmp') }}" external class="text-zinc-300">CMP</flux:link>
            |
            <flux:link variant="ghost" href="{{ route('complaint-procedure') }}" class="text-zinc-300">
                Complaints Procedure
            </flux:link>
        </flux:text>
        <flux:text class="mt-2 text-center text-zinc-300">
            Registered in England & Wales under company number OC454074 at the registered address 1 Coleshill Street,
            Sutton Coldfield, West Midlands, B72 1SD
        </flux:text>
    </div>
</footer>
