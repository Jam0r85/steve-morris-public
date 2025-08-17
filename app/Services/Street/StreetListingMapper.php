<?php

declare(strict_types=1);

namespace App\Services\Street;

use App\Services\Slugs\PropertySlugService;
use App\Services\Street\Mappers\AddressMapper;
use App\Services\Street\Mappers\DetailsMapper;
use App\Services\Street\Mappers\EpcMapper;
use App\Services\Street\Mappers\FeaturesMapper;
use App\Services\Street\Mappers\ListingFields;
use App\Services\Street\Mappers\MediaMapper;
use App\Services\Street\Support\IncludedIndex;
use Carbon\Carbon;
use Throwable;

final class StreetListingMapper
{
    /** @return array{property: array, media: array} */
    public static function mapResource(array $res, array $included, string $channel): array
    {
        $id = $res['id'] ?? null;
        $attr = (array) ($res['attributes'] ?? []);
        $rels = (array) ($res['relationships'] ?? []);
        $inc = new IncludedIndex($included);

        // Base property scaffold
        $status = $attr['status'] ?? null;
        $property = [
            'provider_id' => $id,
            'is_active' => self::isActive($channel, $status),
            'listing_category' => $channel,
            'status' => $status,

            // Sales fields
            'price_sales' => null,
            'price_qualifier' => null,
            'display_price' => null,

            // Lettings fields
            'price_lettings' => null,
            'rent_frequency' => null,
            'deposit' => null,
            'furnished' => null,

            // Basics
            'bedrooms' => $attr['bedrooms'] ?? null,
            'bathrooms' => $attr['bathrooms'] ?? null,
            'receptions' => $attr['receptions'] ?? null,
            'property_type' => $attr['property_type'] ?? null,
            'property_style' => $attr['property_style'] ?? null,

            // Address placeholders (filled next)
            'address_line1' => null,
            'address_town' => null,
            'address_postcode' => null,
            'address_single_line' => null,
            'lat' => null,
            'lng' => null,

            'headline' => null,
            'features' => [],

            'provider_updated_at' => self::parseUtc($attr['updated_at'] ?? null),
            'last_seen_at' => now(),
        ];

        // Address
        $addr = AddressMapper::map($attr, $rels, $inc);
        $property = array_replace($property, $addr);

        // Listing (channel-specific)
        if ('lettings' === $channel) {
            $out = ListingFields::lettings($attr, $rels, $inc);
            $property = array_replace($property, $out['fields']);
            $property['provider_updated_at'] = self::maxDate($property['provider_updated_at'], $out['updated_at']);
        } else {
            $out = ListingFields::sales($attr, $rels, $inc);
            $property = array_replace($property, $out['fields']);
            $property['provider_updated_at'] = self::maxDate($property['provider_updated_at'], $out['updated_at']);
        }

        // ----- Slug & short id (SEO + uniqueness)
        $property['slug'] = PropertySlugService::makeSlug(
            $property['address_single_line'] ?? null,
            $property['address_postcode'] ?? null,
        );
        $property['slug_id'] = PropertySlugService::shortIdFromProvider($property['provider_id']);

        // ----- DETAILS (descriptions + extras)
        $det = DetailsMapper::map($channel, $rels, $inc);

        // retain your existing generic description/headline behaviour
        if ($det['headline'] && empty($property['headline'])) {
            $property['headline'] = $det['headline'];
        }
        $property['provider_updated_at'] = self::maxDate($property['provider_updated_at'], $det['updated_at']);

        // NEW: copy all requested detail fields across (even if description/headline already set)
        $property['council_tax_band'] = $det['council_tax_band'];
        $property['council_tax_cost'] = $det['council_tax_cost'];
        $property['full_description'] = $det['full_description'];
        $property['full_description_lettings'] = $det['full_description_lettings'];
        $property['short_description'] = $det['short_description'];
        $property['short_description_lettings'] = $det['short_description_lettings'];
        $property['service_charge'] = $det['service_charge'];
        $property['ground_rent'] = $det['ground_rent'];
        $property['lease_expiry_date'] = $det['lease_expiry_date']; // YYYY-MM-DD
        $property['heating_system'] = $det['heating_system'];

        // Features (strings from included 'feature' resources)
        $featureNames = FeaturesMapper::map($rels, $inc);
        if ( ! empty($featureNames)) {
            $property['features'] = $featureNames;
        }

        // EPC (rating stored on properties)
        $epc = EpcMapper::map($rels, $inc);
        if ( ! empty($epc['epc_rating'])) {
            $property['epc_rating'] = $epc['epc_rating'];
        }
        $property['provider_updated_at'] = self::maxDate($property['provider_updated_at'], $epc['updated_at']);

        // Media (photos, floorplans, epc)
        $media = MediaMapper::map($rels, $inc);

        return compact('property', 'media');
    }

    // ---------------- helpers ----------------

    private static function isActive(string $channel, ?string $status): bool
    {
        if ( ! $status) {
            return true;
        }
        $s = mb_strtolower($status);
        $inactiveSales = ['sold stc', 'completed', 'exchanged'];
        $inactiveLettings = ['let', 'let agreed'];

        return 'sales' === $channel
            ? ! in_array($s, $inactiveSales, true)
            : ! in_array($s, $inactiveLettings, true);
    }

    private static function parseUtc(?string $iso): ?Carbon
    {
        if ( ! $iso) {
            return null;
        }
        try {
            return Carbon::parse($iso)->utc();
        } catch (Throwable) {
            return null;
        }
    }

    private static function maxDate(?Carbon $a, ?Carbon $b): ?Carbon
    {
        if ($a && $b) {
            return $a->gt($b) ? $a : $b;
        }

        return $a ?: $b;
    }
}
