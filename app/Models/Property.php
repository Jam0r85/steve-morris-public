<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        'provider_id', 'is_active', 'listing_category', 'status',
        'price_sales', 'price_qualifier', 'display_price',
        'price_lettings', 'rent_frequency', 'deposit', 'furnished',
        'epc_rating',
        'branch_id',
        'bedrooms', 'bathrooms', 'receptions',
        'property_type', 'property_style',
        'address_line1', 'address_town', 'address_postcode', 'address_single_line',
        'lat', 'lng', 'headline', 'features',
        'council_tax_band', 'council_tax_cost',
        'full_description', 'full_description_lettings',
        'short_description', 'short_description_lettings',
        'service_charge', 'ground_rent',
        'lease_expiry_date', 'heating_system',
        'slug', 'slug_id',
        'provider_updated_at', 'first_seen_at', 'last_seen_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_price' => 'boolean',
        'features' => 'array',
        'lease_expiry_date' => 'date',
        'provider_updated_at' => 'datetime',
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * Unique hostnames from an images array (for <link rel="preconnect">).
     *
     * @param  array<int, array{url:string, alt?:string}>  $images
     * @return array<int, string>
     */
    public static function hostsFromImages(array $images): array
    {
        return collect($images)
            ->map(fn ($i) => parse_url($i['url'] ?? '', PHP_URL_HOST))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    // === Presentation: description HTML (channel-aware) ==========================
    public function descriptionHtmlForChannel(string $channel): ?string
    {
        $candidates = 'lettings' === $channel
            ? [$this->full_description_lettings, $this->full_description]
            : [$this->full_description, $this->full_description_lettings];

        foreach ($candidates as $html) {
            if (is_string($html) && '' !== mb_trim(strip_tags((string) $html))) {
                return (string) $html;
            }
        }

        return null;
    }

    // === Presentation: features (DB-driven key details) ==========================
    /**
     * @return array<int, array{icon:string,label:string,value:?string}>
     */
    public function featuresKeyDetails(string $channel): array
    {
        $rows = [];

        $money = fn (?int $n) => (is_numeric($n) && (float) $n > 0) ? '£' . number_format((float) $n) : null;
        $labelize = fn (?string $s) => $s ? ucwords(str_replace('_', ' ', mb_trim($s))) : null;

        // Council tax
        $ctBand = mb_trim((string) ($this->council_tax_band ?? ''));
        $ctCost = $money($this->council_tax_cost);
        if ('' !== $ctBand || $ctCost) {
            $rows[] = [
                'icon' => 'home',
                'label' => 'Council tax',
                'value' => mb_trim(($ctBand ? "Band {$ctBand}" : '') . ($ctBand && $ctCost ? ' — ' : '') . ($ctCost ?: '')),
            ];
        }

        // EPC rating
        if ( ! empty($this->epc_rating)) {
            $rows[] = [
                'icon' => 'chart-bar',
                'label' => 'EPC rating',
                'value' => mb_strtoupper((string) $this->epc_rating),
            ];
        }

        // Ground rent / Service charge
        if ( ! empty($this->ground_rent)) {
            $rows[] = ['icon' => 'currency-pound', 'label' => 'Ground rent',    'value' => $money((int) $this->ground_rent) . ' pa'];
        }
        if ( ! empty($this->service_charge)) {
            $rows[] = ['icon' => 'currency-pound', 'label' => 'Service charge', 'value' => $money((int) $this->service_charge) . ' pa'];
        }

        // Lease expiry (casted to date in $casts)
        if ( ! empty($this->lease_expiry_date)) {
            $rows[] = [
                'icon' => 'calendar-days', // if not available in your Flux build, use 'calendar'
                'label' => 'Lease expiry',
                'value' => optional($this->lease_expiry_date)->format('F Y'),
            ];
        }

        // Deposit (lettings)
        if ('lettings' === $channel && ! empty($this->deposit)) {
            $rows[] = ['icon' => 'currency-pound', 'label' => 'Deposit', 'value' => $money((int) $this->deposit)];
        }

        // Furnishing
        if (null !== $this->furnished && '' !== mb_trim((string) $this->furnished)) {
            $rows[] = ['icon' => 'sparkles', 'label' => 'Furnishing', 'value' => $labelize((string) $this->furnished)];
        }

        // Heating
        if ( ! empty($this->heating_system)) {
            $rows[] = ['icon' => 'fire', 'label' => 'Heating', 'value' => (string) $this->heating_system];
        }

        // Style
        if ( ! empty($this->property_style)) {
            $rows[] = ['icon' => 'sparkles', 'label' => 'Style', 'value' => $labelize((string) $this->property_style)];
        }

        return $rows;
    }

    // === Presentation: features (JSON features[] as additional) ==================
    /**
     * Build additional feature rows from the JSON features[] while skipping any
     * labels that collide with key details.
     *
     * @param  array<int,string>  $reservedLabels  Case-insensitive labels to skip
     * @return array<int, array{icon:string,label:string,value:?string}>
     */
    public function featuresAdditional(array $reservedLabels = []): array
    {
        $norm = fn (string $s) => mb_strtolower(mb_trim($s));
        $reserved = array_map($norm, $reservedLabels);

        $labelize = fn (?string $s) => $s ? ucwords(str_replace('_', ' ', mb_trim($s))) : null;

        $out = [];
        $seen = [];

        foreach ((array) ($this->features ?? []) as $f) {
            if ( ! is_string($f) || '' === mb_trim($f)) {
                continue;
            }

            $label = $labelize($f);
            $key = $norm($label);

            if (in_array($key, $reserved, true)) {
                continue;
            }      // skip if collides with a derived label
            if (isset($seen[$key])) {
                continue;
            }                   // de-dupe within array

            $seen[$key] = true;
            $out[] = ['icon' => 'sparkles', 'label' => $label, 'value' => null];
        }

        return $out;
    }

    // === Presentation: combined features ========================================
    /**
     * @return array{derived: array<int, array{icon:string,label:string,value:?string}>, additional: array<int, array{icon:string,label:string,value:?string}>}
     */
    public function featuresForDisplay(string $channel): array
    {
        $derived = $this->featuresKeyDetails($channel);
        $derivedLabels = array_map(fn ($r) => $r['label'], $derived);
        $additional = $this->featuresAdditional($derivedLabels);

        return compact('derived', 'additional');
    }

    public function slugRedirects()
    {
        return $this->hasMany(PropertySlugRedirect::class);
    }

    public function media()
    {
        return $this->hasMany(PropertyMedia::class);
    }

    public function primaryImageUrl(string $size = 'medium'): ?string
    {
        $photo = $this->relationLoaded('media')
            ? $this->media->where('category', 'photo')->sortBy('sort_order')->first()
            : $this->media()->where('category', 'photo')->orderBy('sort_order')->first();

        if ( ! $photo) {
            return null;
        }

        $col = match ($size) {
            'thumbnail' => 'url_thumbnail',
            'small' => 'url_small',
            'medium' => 'url_medium',
            'large' => 'url_large',
            'hero' => 'url_hero',
            'full' => 'url_full',
            default => 'url_medium',
        };

        return $photo->{$col} ?? $photo->url_full ?? $photo->url;
    }

    public function galleryPhotos()
    {
        return $this->relationLoaded('media')
            ? $this->media->where('category', 'photo')->sortBy('sort_order')->values()
            : $this->media()->where('category', 'photo')->orderBy('sort_order')->get();
    }

    public function floorplans()
    {
        return $this->relationLoaded('media')
            ? $this->media->where('category', 'floorplan')->sortBy('sort_order')->values()
            : $this->media()->where('category', 'floorplan')->orderBy('sort_order')->get();
    }

    /* ------------------------------- State helpers ------------------------------ */

    public function isActive(): bool
    {
        return 1 === (int) $this->is_active;
    }

    public function isNewListing(int $days = 7): bool
    {
        return $this->isActive() && optional($this->created_at)->gt(now()->subDays($days));
    }

    public function isReduced(): bool
    {
        return $this->isActive() && (bool) ($this->price_reduced ?? false);
    }

    /* ---------------------------- Presentation helpers -------------------------- */

    /**
     * Human-readable property type label.
     * Falls back from property_type → property_style → 'Property'.
     */
    public function typeLabel(): string
    {
        $raw = $this->property_type ?: $this->property_style ?: null;

        return $raw ? ucwords(str_replace('_', ' ', (string) $raw)) : 'Property';
    }

    /**
     * Numeric price for a channel ('sales' | 'lettings').
     */
    public function priceForChannel(string $channel): ?int
    {
        return 'sales' === $channel ? $this->price_sales : $this->price_lettings;
    }

    /**
     * Human-readable price for a channel. For lettings, appends frequency (pcm by default).
     */
    public function priceTextForChannel(string $channel): string
    {
        $price = $this->priceForChannel($channel);
        if ( ! is_numeric($price)) {
            return 'POA';
        }
        if ('sales' === $channel) {
            return '£' . number_format($price);
        }
        $freq = mb_strtoupper($this->rent_frequency ?? 'pcm');

        return '£' . number_format($price) . ' ' . $freq;
    }

    /**
     * Status badge label for the card header when inactive (null when active).
     */
    public function badgeTextForChannel(string $channel): ?string
    {
        return $this->isActive() ? null : ('sales' === $channel ? 'Sold STC' : 'Applied For');
    }

    /**
     * Gallery images for hero: [['url'=>..., 'alt'=>...], ...]
     * Uses galleryPhotos() and falls back to primary image if empty.
     */
    public function galleryImagesLarge(): array
    {
        $items = $this->galleryPhotos();

        $images = collect($items)->map(function ($m) {
            /** @var PropertyMedia $m */
            $url = $m->url_hero ?? $m->url_large ?? $m->url_full ?? $m->url ?? null;

            return $url ? ['url' => $url, 'alt' => $this->address_single_line] : null;
        })->filter();

        if ($images->isEmpty()) {
            if ($url = $this->primaryImageUrl('large')) {
                $images = collect([['url' => $url, 'alt' => $this->address_single_line]]);
            }
        }

        return $images->values()->all();
    }

    /**
     * Fetch similar properties (active, same channel/town, ±1 bed, not this one).
     */
    public function similar(string $channel, int $limit = 6)
    {
        return static::query()
            ->active(true)
            ->where('listing_category', $channel)
            ->inTown($this->address_town)
            ->bedsAtLeast(max(1, (int) $this->bedrooms - 1))
            ->bedsAtMost((int) $this->bedrooms + 1)
            ->whereKeyNot($this->getKey())
            ->withPrimaryMedia()
            ->limit($limit)
            ->get();
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'uuid');
    }

    public function seoTitle(): string
    {
        $type = $this->property_type ?: 'Property';
        $bedrooms = $this->bedrooms ? "{$this->bedrooms} Bed" : '';
        $town = $this->address_town ?: '';
        $postcode = $this->address_postcode ?: '';

        return mb_trim(implode(' ', array_filter([
            $bedrooms,
            $type,
            $town ? "in {$town}," : '',
            $postcode,
        ])));
    }

    protected static function booted(): void
    {
        static::updating(function (self $model): void {
            if ($model->isDirty('slug')) {
                $old = $model->getOriginal('slug');
                if ($old) {
                    $model->slugRedirects()->create(['old_slug' => $old]);
                }
            }
        });
    }

    /* --------------------------------- Scopes ----------------------------------- */

    #[Scope]
    protected function active(Builder $q, bool $active = true): void
    {
        $q->where('is_active', $active);
    }

    #[Scope]
    protected function sales(Builder $q): void
    {
        $q->where('listing_category', 'sales');
    }

    #[Scope]
    protected function lettings(Builder $q): void
    {
        $q->where('listing_category', 'lettings');
    }

    #[Scope]
    protected function priceSalesBetween(Builder $q, ?int $min, ?int $max): void
    {
        $q->when(null !== $min, fn ($qq) => $qq->where('price_sales', '>=', $min))
            ->when(null !== $max, fn ($qq) => $qq->where('price_sales', '<=', $max));
    }

    #[Scope]
    protected function priceLettingsBetween(Builder $q, ?int $min, ?int $max): void
    {
        $q->when(null !== $min, fn ($qq) => $qq->where('price_lettings', '>=', $min))
            ->when(null !== $max, fn ($qq) => $qq->where('price_lettings', '<=', $max));
    }

    #[Scope]
    protected function bedsAtLeast(Builder $q, ?int $min): void
    {
        $q->when(null !== $min, fn ($qq) => $qq->where('bedrooms', '>=', $min));
    }

    #[Scope]
    protected function bedsAtMost(Builder $q, ?int $max): void
    {
        $q->when(null !== $max, fn ($qq) => $qq->where('bedrooms', '<=', $max));
    }

    #[Scope]
    protected function inTown(Builder $q, ?string $town): void
    {
        $q->when($town, fn ($qq) => $qq->where('address_town', $town));
    }

    #[Scope]
    protected function postcodeSector(Builder $q, ?string $postcode): void
    {
        if ( ! $postcode) {
            return;
        }
        $outward = mb_strtoupper(mb_trim(explode(' ', $postcode)[0] ?? $postcode));
        $q->where('address_postcode', 'like', $outward . '%');
    }

    #[Scope]
    protected function hasFeature(Builder $q, ?string $name): void
    {
        $q->when($name, fn ($qq) => $qq->whereJsonContains('features', $name));
    }

    #[Scope]
    protected function hasAnyFeatures(Builder $q, array $names): void
    {
        if ( ! count($names)) {
            return;
        }
        $q->where(function ($w) use ($names): void {
            foreach ($names as $n) {
                $w->orWhereJsonContains('features', $n);
            }
        });
    }

    #[Scope]
    protected function sortParam(Builder $q, ?string $sort, ?string $channel = null): void
    {
        $sort = $sort ?: 'newest';
        $priceCol = 'lettings' === $channel ? 'price_lettings' : 'price_sales';

        match ($sort) {
            'price_asc' => $q->orderBy($priceCol)->orderBy('id', 'desc'),
            'price_desc' => $q->orderByDesc($priceCol)->orderBy('id', 'desc'),
            'beds_asc' => $q->orderBy('bedrooms')->orderBy('id', 'desc'),
            'beds_desc' => $q->orderByDesc('bedrooms')->orderBy('id', 'desc'),
            default => $q->orderByDesc('provider_updated_at')->orderByDesc('id'),
        };
    }

    #[Scope]
    protected function withPrimaryMedia(Builder $q): void
    {
        $q->with(['media' => fn ($m) => $m->where('category', 'photo')->orderBy('sort_order')->limit(1)]);
    }

    #[Scope]
    protected function applyFilters(Builder $q, array $f, string $channel): void
    {
        'lettings' === $channel ? $q->lettings() : $q->sales();

        $q->active()
            ->bedsAtLeast($f['min_beds'] ?? null)
            ->bedsAtMost($f['max_beds'] ?? null)
            ->inTown($f['town'] ?? null)
            ->postcodeSector($f['postcode'] ?? null);

        'lettings' === $channel
            ? $q->priceLettingsBetween($f['min_price'] ?? null, $f['max_price'] ?? null)
            : $q->priceSalesBetween($f['min_price'] ?? null, $f['max_price'] ?? null);

        ! empty($f['features']) && is_array($f['features'])
            ? $q->hasAnyFeatures($f['features'])
            : $q->hasFeature($f['feature'] ?? null);

        $q->sortParam($f['sort'] ?? null, $channel);
    }
}
