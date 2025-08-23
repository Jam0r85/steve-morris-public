<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    /**
     * Use UUID primary key
     */
    public $incrementing = false;

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    /**
     * Fillable fields
     */
    protected $fillable = [
        'uuid',
        'name', 'public_name', 'email_address', 'telephone', 'website',
        'address_single_line', 'address_anon_single_line',
        'address_building_number', 'address_building_name',
        'address_street', 'address_line_1', 'address_line_2',
        'address_line_3', 'address_line_4', 'address_town',
        'address_country', 'address_postcode', 'address_udprn',
    ];

    /**
     * Appended attributes for toArray(), API responses, and Blade
     */
    protected $appends = [
        'display_name',
        'email',
        'phone',
        'tel_href',
        'website_url',
        'address_full',
        'address_public',
        'maps_href',
    ];

    /**
     * Relationships
     */
    public function properties()
    {
        return $this->hasMany(Property::class, 'branch_id', 'id');
    }

    /* =========================================================================
     |  Scopes
     | ========================================================================= */

    /**
     * Order branches by public_name, falling back to name
     */
    public function scopePublicOrder($query)
    {
        return $query->orderByRaw('COALESCE(NULLIF(public_name, \'\'), name) asc');
    }

    /* =========================================================================
     |  Utility Methods (optional extras)
     | ========================================================================= */

    /**
     * Compact "Town Postcode" label
     */
    public function townPostcode(): string
    {
        return mb_trim("{$this->address_town} {$this->address_postcode}");
    }

    /* =========================================================================
     |  Accessors
     | ========================================================================= */

    /**
     * Display Name (public_name preferred, fallback to name)
     */
    protected function displayName(): Attribute
    {
        return Attribute::get(fn () => $this->public_name ?: $this->name);
    }

    /**
     * Email alias for email_address
     */
    protected function email(): Attribute
    {
        return Attribute::get(fn () => $this->email_address);
    }

    /**
     * Phone alias for telephone
     */
    protected function phone(): Attribute
    {
        return Attribute::get(fn () => $this->telephone);
    }

    /**
     * Full address (composed from parts if no single_line provided)
     */
    protected function addressFull(): Attribute
    {
        return Attribute::get(function () {
            if ($this->address_single_line) {
                return $this->address_single_line;
            }

            $firstLine = mb_trim(implode(' ', array_filter([
                $this->address_building_number,
                $this->address_building_name,
            ])));

            $streetOrLine1 = $this->address_street ?: $this->address_line_1;

            $parts = array_filter([
                $firstLine ?: null,
                $streetOrLine1,
                $this->address_line_2,
                $this->address_line_3,
                $this->address_line_4,
                $this->address_town,
                $this->address_postcode,
                $this->address_country,
            ]);

            return implode(', ', $parts);
        });
    }

    /**
     * Public address (anonymised single line if provided)
     */
    protected function addressPublic(): Attribute
    {
        return Attribute::get(fn () => $this->address_anon_single_line ?: $this->address_full);
    }

    /**
     * Tel: link (strips spaces and symbols from telephone)
     */
    protected function telHref(): Attribute
    {
        return Attribute::get(function () {
            $raw = (string) $this->telephone;
            $sanitised = preg_replace('/[^\d+]/', '', $raw);

            return $sanitised ? "tel:{$sanitised}" : null;
        });
    }

    /**
     * Website URL (ensures http(s) scheme)
     */
    protected function websiteUrl(): Attribute
    {
        return Attribute::get(function () {
            $url = mb_trim((string) $this->website);
            if ('' === $url) {
                return null;
            }
            if ( ! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
                $url = "https://{$url}";
            }

            return $url;
        });
    }

    /**
     * Google Maps link (lightweight, query-based)
     */
    protected function mapsHref(): Attribute
    {
        return Attribute::get(function () {
            $query = urlencode($this->address_full ?? '');

            return $query ? "https://www.google.com/maps/search/?api=1&query={$query}" : null;
        });
    }
}
