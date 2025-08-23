@props([
    'level' => 2,
    'align' => 'center',
    'id' => null,
    'slugMax' => 80,
])

@php
    use Illuminate\Support\Str;

    $alignClass = $align === 'center' ? 'text-center' : 'text-left';
    $base = 'text-3xl font-bold sm:text-4xl';

    // Auto-generate id if none provided
    $rawText = trim(preg_replace('/\s+/', ' ', strip_tags((string) $slot)));
    $computedId = $id ?: Str::slug(Str::limit($rawText, (int) $slugMax, ''));
@endphp

<flux:heading :level="$level" size="xl" id="{{ $computedId }}" class="{{ $base . ' ' . $alignClass }}">
    {{ $slot }}
</flux:heading>
