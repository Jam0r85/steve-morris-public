<?php

declare(strict_types=1);

namespace App\Mappers;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Throwable;

class StreetListingMapper
{
    /** @var Collection<string,array> */
    protected Collection $included;

    public function __construct(
        protected string $category,   // 'sales' | 'lettings'
        array $included               // raw included array from API
    ) {
        $this->included = collect($included)->keyBy(
            fn ($i) => ($i['type'] ?? '') . ':' . ($i['id'] ?? '')
        );
    }

    /**
     * Map one listing resource → [propertyPayload, mediaPayloads[]]
     *
     * @return array{0: array, 1: array[]}
     */
    public function map(array $listing): array
    {
        $attr = (array) ($listing['attributes'] ?? []);
        $rels = (array) ($listing['relationships'] ?? []);

        // Included resources (if present)
        $address = $this->resolveSingle($rels, 'address');           // type address
        $details = $this->resolveSingle($rels, 'details');           // type details
        $epc = $this->resolveSingle($rels, 'epc');               // type epc
        $salesListing = $this->resolveSingle($rels, 'salesListing');      // type sales_listing
        $lettingsListing = $this->resolveSingle($rels, 'lettingsListing');   // type lettings_listing

        $providerId = $listing['id'] ?? null;

        // --- Address (prefer public_address for address_single_line) ---
        $publicAddress = $attr['public_address'] ?? null; // e.g. "Main Street, Follifoot, HG3"
        $inlineAddress = $address['attributes']['inline'] ?? ($attr['inline_address'] ?? null);
        $addressSingle = $publicAddress ?: $inlineAddress;
        $postcode = $address['attributes']['postcode'] ?? ($attr['postcode'] ?? null);
        $line1 = $address['attributes']['line_1'] ?? null;
        $town = $address['attributes']['town'] ?? null;
        $lat = $address['attributes']['latitude'] ?? null;
        $lng = $address['attributes']['longitude'] ?? null;

        // Slug + short ID (short ID derived from provider UUID; never equal to provider_id)
        $slug = $this->makeSlug($addressSingle);
        $slugId = $providerId ? $this->shortIdFromProvider($providerId) : null;

        // --- Pricing (prefer listing resources; fallback to top-level attrs) ---
        $priceSales = null;
        $priceQualifier = null;
        $displayPrice = null;

        if ($salesListing) {
            $sa = (array) ($salesListing['attributes'] ?? []);
            $priceSales = $sa['price'] ?? null;
            $priceQualifier = $sa['price_qualifier'] ?? null;
            $displayPrice = $sa['display_price'] ?? $displayPrice;
        } elseif (isset($attr['sale_price'])) {
            $priceSales = $attr['sale_price'];
            $priceQualifier = $attr['price_qualifier'] ?? null;
            $displayPrice = $attr['display_price'] ?? $displayPrice;
        }

        $priceLettings = null;
        $rentFrequency = null; // pcm | ppw
        $deposit = null;
        $furnished = null;

        if ($lettingsListing) {
            $la = (array) ($lettingsListing['attributes'] ?? []);
            if (isset($la['price_pcm'])) {
                $priceLettings = $la['price_pcm'];
                $rentFrequency = 'pcm';
            } elseif (isset($la['price_ppw'])) {
                $priceLettings = $la['price_ppw'];
                $rentFrequency = 'ppw';
            }
            $deposit = $la['deposit'] ?? null;
            $furnished = $la['furnished'] ?? null;
            $displayPrice = $la['display_price'] ?? $displayPrice;
        } elseif (isset($attr['letting_price'])) {
            $priceLettings = $attr['letting_price'];
            $rentFrequency = $attr['rent_frequency'] ?? null;
            $deposit = $attr['deposit'] ?? null;
            $furnished = $attr['furnished'] ?? null;
            $displayPrice = $attr['display_price'] ?? $displayPrice;
        }

        // Default frequency: if we have a lettings price but no frequency, assume pcm
        if (null !== $priceLettings && null === $rentFrequency) {
            $rentFrequency = 'pcm';
        }

        // --- Status & is_active logic ---
        // Prefer the channel’s listing status; fall back to top-level sale_status/letting_status, then status.
        $salesStatus = $salesListing['attributes']['status'] ?? ($attr['sale_status'] ?? null);
        $lettingsStatus = $lettingsListing['attributes']['status'] ?? ($attr['letting_status'] ?? null);

        $channelStatus = 'sales' === $this->category
            ? ($salesListing['attributes']['status'] ?? $salesStatus)
            : ($lettingsListing['attributes']['status'] ?? $lettingsStatus);

        if ( ! $channelStatus) {
            // Finally, fall back to top-level status if nothing else present
            $channelStatus = $attr['status'] ?? null;
        }

        // Inactive iff any status equals the given labels (case-insensitive)
        $inactiveLabels = ['let agreed', 'sold stc'];
        $statusesToCheck = array_filter([
            $salesStatus,
            $lettingsStatus,
            $attr['status'] ?? null,
        ]);

        $isInactive = false;
        foreach ($statusesToCheck as $s) {
            if ($s && in_array(mb_strtolower(mb_trim($s)), $inactiveLabels, true)) {
                $isInactive = true;
                break;
            }
        }
        $isActive = ! $isInactive;

        $property = [
            'provider_id' => $providerId,
            'slug_id' => $slugId,                    // short token (NOT provider UUID)
            'slug' => $slug,
            'branch_id' => $attr['branch_uuid'] ?? null,
            'listing_category' => $this->category,
            'status' => $channelStatus,
            'is_active' => $isActive,
            'provider_updated_at' => $this->asUtc($attr['updated_at'] ?? null),

            // basics
            'bedrooms' => $attr['bedrooms'] ?? null,
            'bathrooms' => $attr['bathrooms'] ?? null,
            'receptions' => $attr['receptions'] ?? null,
            'property_type' => $attr['property_type'] ?? null,
            'property_style' => $attr['property_style'] ?? null,

            // address
            'address_line1' => $line1,
            'address_town' => $town,
            'address_postcode' => $postcode,
            'address_single_line' => $addressSingle,
            'lat' => $lat,
            'lng' => $lng,

            // pricing
            'price_sales' => $priceSales,
            'price_qualifier' => $priceQualifier,
            'display_price' => $displayPrice,

            'price_lettings' => $priceLettings,
            'rent_frequency' => $rentFrequency,
            'deposit' => $deposit,
            'furnished' => $furnished,

            // EPC
            'epc_rating' => $epc['attributes']['rating'] ?? null,

            // details
            'headline' => $details['attributes']['short_description'] ?? null,
            'full_description' => $details['attributes']['full_description'] ?? null,
            'full_description_lettings' => $details['attributes']['full_description_lettings'] ?? null,
            'short_description' => $details['attributes']['short_description'] ?? null,
            'short_description_lettings' => $details['attributes']['short_description_lettings'] ?? null,
            'council_tax_band' => $details['attributes']['council_tax_band'] ?? null,
            'council_tax_cost' => $details['attributes']['council_tax_cost'] ?? null,
            'service_charge' => $details['attributes']['service_charge'] ?? null,
            'ground_rent' => $details['attributes']['ground_rent'] ?? null,
            'lease_expiry_date' => $details['attributes']['lease_expiry_date']
                                         ?? ($details['attributes']['ground_rent_expiry'] ?? null),
            'heating_system' => $details['attributes']['heating_system'] ?? null,

            // features (string array)
            'features' => $this->mapFeatures($rels),
            'last_seen_at' => now(),
        ];

        $media = array_merge(
            $this->mapMediaMany($rels, 'images', 'photo'),
            $this->mapMediaMany($rels, 'floorplans', 'floorplan'),
            $this->mapMediaSingle($rels, 'epc', 'epc')
        );

        return [$property, $media];
    }

    // ------------------ slug helpers (embedded) ------------------

    private function makeSlug(?string $addressSingleLine): ?string
    {
        if ( ! $addressSingleLine) {
            return null;
        }
        $slug = Str::slug(mb_trim($addressSingleLine), '-');

        return '' !== $slug ? $slug : null;
    }

    private function shortIdFromProvider(string $providerUuid, int $length = 6): string
    {
        // Deterministic: unsigned crc32 (decimal) → base36 → pad → trim to length
        $crc = sprintf('%u', crc32($providerUuid));
        $base36 = base_convert($crc, 10, 36);
        $padded = mb_str_pad(mb_strtolower($base36), $length, '0', STR_PAD_LEFT);

        return mb_substr($padded, 0, $length);
    }

    // ------------------ mapping helpers ------------------

    private function resolveSingle(array $rels, string $key): ?array
    {
        $node = $rels[$key]['data'] ?? null;
        if ( ! is_array($node)) {
            return null;
        }
        $rid = ($node['type'] ?? '') . ':' . ($node['id'] ?? '');

        return $this->included->get($rid);
    }

    private function resolveMany(array $rels, string $key): array
    {
        $out = [];
        foreach ($rels[$key]['data'] ?? [] as $node) {
            $rid = ($node['type'] ?? '') . ':' . ($node['id'] ?? '');
            $res = $this->included->get($rid);
            if ($res) {
                $out[] = $res;
            }
        }

        return $out;
    }

    private function mapFeatures(array $rels): ?array
    {
        $out = [];
        foreach ($this->resolveMany($rels, 'features') as $res) {
            $name = $res['attributes']['name'] ?? null;
            if ($name) {
                $out[] = $name;
            }
        }

        return $out ?: null;
    }

    private function mapMediaMany(array $rels, string $relKey, string $category): array
    {
        $out = [];
        foreach ($this->resolveMany($rels, $relKey) as $res) {
            $out[] = $this->mediaPayloadFromIncluded($res, $category);
        }

        return array_values(array_filter($out));
    }

    private function mapMediaSingle(array $rels, string $relKey, string $category): array
    {
        $res = $this->resolveSingle($rels, $relKey);
        if ( ! $res) {
            return [];
        }
        $payload = $this->mediaPayloadFromIncluded($res, $category);

        return $payload ? [$payload] : [];
    }

    private function mediaPayloadFromIncluded(array $res, string $category): ?array
    {
        $a = (array) ($res['attributes'] ?? []);
        $urls = (array) ($a['urls'] ?? []);

        if (empty($a['url'])) {
            return null;
        }

        return [
            'category' => $category,                // photo | floorplan | epc
            'url' => $a['url'],
            'sort_order' => $a['order'] ?? null,
            'is_image' => $a['is_image'] ?? null,
            'media_type' => $a['media_type'] ?? null,
            'title' => $a['title'] ?? null,
            'url_thumbnail' => $urls['thumbnail'] ?? null,
            'url_small' => $urls['small'] ?? null,
            'url_medium' => $urls['medium'] ?? null,
            'url_large' => $urls['large'] ?? null,
            'url_hero' => $urls['hero'] ?? null,
            'url_full' => $urls['full'] ?? null,
        ];
    }

    private function asUtc(?string $iso): ?string
    {
        if ( ! $iso) {
            return null;
        }
        try {
            return Carbon::parse($iso)->utc()->toDateTimeString();
        } catch (Throwable) {
            return null;
        }
    }
}
