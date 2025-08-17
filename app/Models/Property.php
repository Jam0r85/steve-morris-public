<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

final class Property extends Model
{
    protected $fillable = [
        'provider_id', 'is_active', 'listing_category', 'status',
        'price_sales', 'price_qualifier', 'display_price',
        'price_lettings', 'rent_frequency', 'deposit', 'furnished',
        'epc_rating',
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
        $q->when(null !== $min, fn($qq) => $qq->where('price_sales', '>=', $min))
            ->when(null !== $max, fn($qq) => $qq->where('price_sales', '<=', $max));
    }

    #[Scope]
    protected function priceLettingsBetween(Builder $q, ?int $min, ?int $max): void
    {
        $q->when(null !== $min, fn($qq) => $qq->where('price_lettings', '>=', $min))
            ->when(null !== $max, fn($qq) => $qq->where('price_lettings', '<=', $max));
    }

    #[Scope]
    protected function bedsAtLeast(Builder $q, ?int $min): void
    {
        $q->when(null !== $min, fn($qq) => $qq->where('bedrooms', '>=', $min));
    }

    #[Scope]
    protected function bedsAtMost(Builder $q, ?int $max): void
    {
        $q->when(null !== $max, fn($qq) => $qq->where('bedrooms', '<=', $max));
    }

    #[Scope]
    protected function inTown(Builder $q, ?string $town): void
    {
        $q->when($town, fn($qq) => $qq->where('address_town', $town));
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
        $q->when($name, fn($qq) => $qq->whereJsonContains('features', $name));
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
        $q->with(['media' => fn($m) => $m->where('category', 'photo')->orderBy('sort_order')->limit(1)]);
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
