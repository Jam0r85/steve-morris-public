@props([
    'width' => 'content',
])

{{-- Width wrapper (no padding/margins here) --}}
@if ($width === 'container')
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
@endif

<flux:separator class="dark:bg-zinc-700" />

@if ($width === 'container' || $width === 'bleed')
  </div>
@endif
