@props([
    'width' => 'container',
    'gap' => 'mt-16',
])

<section {{ $attributes }}>
    @if ($width === 'container')
        <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            @if (isset($heading) || isset($content))
                @isset($heading)
                    <div>{{ $heading }}</div>
                @endisset

                @isset($content)
                    <div class="{{ $gap }}">{{ $content }}</div>
                @endisset
            @else
                {{-- Backwards compatibility: render slot directly if named slots aren't used --}}
                {{ $slot }}
            @endif
        </div>
    @else
        <div class="px-6 py-24 sm:py-32 lg:px-8">
            @if (isset($heading) || isset($content))
                @isset($heading)
                    <div>{{ $heading }}</div>
                @endisset

                @isset($content)
                    <div class="{{ $gap }}">{{ $content }}</div>
                @endisset
            @else
                {{ $slot }}
            @endif
        </div>
    @endif
</section>
