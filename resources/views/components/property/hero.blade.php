@props([
  'images' => [],
  'badgeText' => null,
  'badgeColor' => 'bg-red-600',
])

<livewire:properties.hero-gallery
  :images="$images"
  :badge-text="$badgeText"
  badge-color="{{ $badgeColor }}"
/>
