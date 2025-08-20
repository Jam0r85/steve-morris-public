<?php

declare(strict_types=1);

namespace App\Livewire\Properties;

use Livewire\Component;

class HeroGallery extends Component
{
    /** Normalized to [['src' => string, 'alt' => string], ...] */
    public array $images = [];

    public int $index = 0;

    /** Tailwind height classes, can be overridden from Blade attribute */
    public string $height = 'h-[70vh] md:h-[72vh] max-h-[820px] min-h-[380px]';

    public function mount(array $images = [], int $start = 0): void
    {
        // Accept your Property::galleryImagesLarge() => [['url','alt'],...]
        // Also tolerates ['src'], ['display'], or plain string URLs.
        $normalized = collect($images)->map(function ($p) {
            if (is_string($p)) {
                return ['src' => $p, 'alt' => 'Property photo'];
            }

            $src = $p['src'] ?? $p['url'] ?? $p['display'] ?? null;
            if ( ! $src) {
                return null;
            }

            $alt = $p['alt'] ?? $p['label'] ?? $p['caption'] ?? 'Property photo';

            return ['src' => $src, 'alt' => $alt];
        })->filter()->values()->all();

        $this->images = $normalized;
        $count = count($this->images);
        $this->index = $count ? max(0, min($start, $count - 1)) : 0;
    }

    public function next(): void
    {
        if ( ! $this->images) {
            return;
        }
        $this->index = ($this->index + 1) % count($this->images);
    }

    public function prev(): void
    {
        if ( ! $this->images) {
            return;
        }
        $this->index = ($this->index - 1 + count($this->images)) % count($this->images);
    }

    public function goTo(int $i): void
    {
        if ($i >= 0 && $i < count($this->images)) {
            $this->index = $i;
        }
    }

    public function render()
    {
        return view('livewire.properties.hero-gallery');
    }
}
