@props([
    "heading" => "Send us a message",
    "phone" => "0121 000 0000",
    "brochureUrl" => null,
])

<div class="lg:sticky lg:top-6 space-y-4">
    <flux:card>
        <div class="p-4 md:p-6 space-y-4">
            <x-flux::heading
                level="3"
                size="lg"
                class="flex items-center gap-2"
            >
                <flux:icon.chat-bubble-left-right
                    class="w-5 h-5 text-zinc-400"
                />
                {{ $heading }}
            </x-flux::heading>

            <form method="post" action="#" class="space-y-3">
                @csrf
                <flux:field>
                    <flux:label>Full name</flux:label>
                    <flux:input
                        type="text"
                        name="name"
                        placeholder="Jane Smith"
                    />
                </flux:field>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <flux:field>
                        <flux:label>Email</flux:label>
                        <flux:input
                            type="email"
                            name="email"
                            placeholder="you@example.com"
                        />
                    </flux:field>
                    <flux:field>
                        <flux:label>Phone</flux:label>
                        <flux:input type="tel" name="phone" placeholder="07…" />
                    </flux:field>
                </div>

                <flux:field>
                    <flux:label>Message</flux:label>
                    <flux:textarea
                        name="message"
                        rows="4"
                        placeholder="I’d like to arrange a viewing…"
                    ></flux:textarea>
                </flux:field>

                <x-flux::button type="submit" class="w-full" variant="primary">
                    Send enquiry
                </x-flux::button>
            </form>

            @if ($brochureUrl)
                <a
                    href="{{ $brochureUrl }}"
                    target="_blank"
                    rel="noopener"
                    class="block text-center text-sm text-blue-700 underline"
                >
                    Download brochure (PDF)
                </a>
            @endif

            <div class="pt-3 border-t text-xs text-zinc-500">
                Prefer to talk?
                <a
                    href="tel:{{ preg_replace("/\D+/", "", $phone) }}"
                    class="text-blue-700 underline"
                >
                    {{ $phone }}
                </a>
            </div>
        </div>
    </flux:card>
</div>
