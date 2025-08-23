<div class="lg:sticky lg:top-[calc(var(--header-h)+1.5rem)]">
    <section class="relative isolate">
        <div class="bg-accent/8 pointer-events-none absolute inset-0 -z-10 rounded-3xl blur-xl"></div>

        <div
            class="overflow-hidden rounded-2xl bg-white/80 shadow-lg ring-1 ring-zinc-900/5 backdrop-blur dark:bg-zinc-900/80 dark:ring-white/10"
        >
            <div class="space-y-5 p-5 sm:p-6">
                @if ($submitted)
                    <flux:callout
                        variant="success"
                        icon="check-circle"
                        heading="Your message has been sent."
                        role="status"
                    >
                        Thanks — we’ve received your enquiry and will be in touch as soon as possible. If it’s urgent,
                        please call
                        <a
                            href="tel:{{ preg_replace('/\D+/', '', $property->branch_phone ?? '01210000000') }}"
                            class="underline"
                        >
                            {{ $property->branch_phone ?? '0121 000 0000' }}
                        </a>
                        .
                    </flux:callout>
                @else
                    <h3 class="text-center text-base font-semibold text-zinc-900 dark:text-white">Submit an Enquiry</h3>

                    @if ($errorMessage)
                        <flux:callout
                            variant="destructive"
                            icon="circle-alert"
                            heading="We couldn’t send your message"
                            role="alert"
                        >
                            {{ $errorMessage }}
                        </flux:callout>
                    @endif

                    <form wire:submit.prevent="submit" class="space-y-4" novalidate>
                        @csrf

                        {{-- Honeypot --}}
                        <input
                            type="text"
                            name="website"
                            wire:model.defer="website"
                            class="hidden"
                            tabindex="-1"
                            autocomplete="off"
                        />

                        {{-- First + Last name --}}
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <flux:field>
                                <flux:label for="first_name" class="sr-only">First name</flux:label>
                                <flux:input
                                    id="first_name"
                                    type="text"
                                    placeholder="First name"
                                    aria-invalid="@error('first_name') true @else false @enderror"
                                    aria-describedby="first_name_error"
                                    wire:model.defer="first_name"
                                />
                                @error('first_name')
                                    <div id="first_name_error" class="mt-1 text-xs text-red-600">{{ $message }}</div>
                                @enderror
                            </flux:field>

                            <flux:field>
                                <flux:label for="last_name" class="sr-only">Last name</flux:label>
                                <flux:input
                                    id="last_name"
                                    type="text"
                                    placeholder="Last name"
                                    aria-invalid="@error('last_name') true @else false @enderror"
                                    aria-describedby="last_name_error"
                                    wire:model.defer="last_name"
                                />
                                @error('last_name')
                                    <div id="last_name_error" class="mt-1 text-xs text-red-600">{{ $message }}</div>
                                @enderror
                            </flux:field>
                        </div>

                        {{-- Email --}}
                        <flux:field>
                            <flux:label for="email" class="sr-only">Email</flux:label>
                            <flux:input
                                id="email"
                                type="email"
                                placeholder="Email address"
                                aria-invalid="@error('email') true @else false @enderror"
                                aria-describedby="email_error"
                                wire:model.defer="email"
                            />
                            @error('email')
                                <div id="email_error" class="mt-1 text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </flux:field>

                        {{-- Telephone --}}
                        <flux:field>
                            <flux:label for="telephone_number" class="sr-only">Telephone number</flux:label>
                            <flux:input
                                id="telephone_number"
                                type="tel"
                                placeholder="Telephone number"
                                aria-invalid="@error('telephone_number') true @else false @enderror"
                                aria-describedby="telephone_error"
                                wire:model.defer="telephone_number"
                            />
                            @error('telephone_number')
                                <div id="telephone_error" class="mt-1 text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </flux:field>

                        {{-- Message --}}
                        <flux:field>
                            <flux:label for="enq_message" class="sr-only">Message</flux:label>
                            <flux:textarea
                                id="enq_message"
                                rows="4"
                                placeholder="Message"
                                aria-invalid="@error('message') true @else false @enderror"
                                aria-describedby="message_error"
                                wire:model.defer="message"
                            />
                            @error('message')
                                <div id="message_error" class="mt-1 text-xs text-red-600">{{ $message }}</div>
                            @enderror
                        </flux:field>

                        {{-- Switches (inline) --}}
                        <flux:field variant="inline">
                            <flux:switch
                                id="switch_request_viewing"
                                name="request_viewing"
                                value="1"
                                wire:model="request_viewing"
                            />
                            <flux:label for="switch_request_viewing">Request a viewing</flux:label>
                        </flux:field>

                        <flux:field variant="inline">
                            <flux:switch
                                id="switch_request_valuation"
                                name="request_valuation"
                                value="1"
                                wire:model="request_valuation"
                            />
                            <flux:label for="switch_request_valuation">Request a valuation</flux:label>
                        </flux:field>

                        <x-flux::button
                            type="submit"
                            class="w-full"
                            variant="primary"
                            wire:loading.attr="disabled"
                            wire:target="submit"
                            aria-live="polite"
                        >
                            <span wire:loading.remove wire:target="submit">Send enquiry</span>
                            <span wire:loading wire:target="submit">Sending…</span>
                        </x-flux::button>

                        @if ($property->branch?->telephone)
                            <flux:text size="lg" class="text-center">
                                Prefer to talk?
                                <flux:link href="tel:{{ $property->branch->telephone }}" target="_blank">
                                    {{ $property->branch->telephone }}
                                </flux:link>
                            </flux:text>
                        @endif

                        <flux:text size="xs" class="text-center">
                            By submitting you agree to our
                            <flux:link href="{{ route('privacy-policy') }}" target="_blank">privacy policy</flux:link>
                        </flux:text>
                    </form>
                @endif
            </div>
        </div>
    </section>
</div>
