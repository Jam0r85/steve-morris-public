<?php

declare(strict_types=1);

namespace App\Services\Street\Mappers;

use App\Services\Street\Support\IncludedIndex;

final class AddressMapper
{
    /**
     * Build address fields from property attributes + included address (if present).
     *
     * @return array{address_line1:?string,address_town:?string,address_postcode:?string,address_single_line:?string,lat:?float,lng:?float,headline:?string}
     */
    public static function map(array $attributes, array $relationships, IncludedIndex $inc): array
    {
        $addressLine1 = $attributes['inline_address'] ?? null;
        $publicAddress = $attributes['public_address'] ?? null;
        $postcode = $attributes['postcode'] ?? null;
        $headline = $publicAddress ?: $addressLine1;
        $town = null;
        $lat = null;
        $lng = null;

        $addressId = $relationships['address']['data']['id'] ?? null;
        if ($addressId) {
            $addr = $inc->get('address', $addressId);
            if ($addr) {
                $addressLine1 = $addr['line_1'] ?? $addressLine1;
                $town = $addr['town'] ?? $town;
                $postcode = $addr['postcode'] ?? $postcode;
                $lat = $addr['latitude'] ?? $lat;
                $lng = $addr['longitude'] ?? $lng;
            }
        }

        return [
            'address_line1' => $addressLine1,
            'address_town' => $town,
            'address_postcode' => $postcode,
            'address_single_line' => $publicAddress,
            'lat' => $lat,
            'lng' => $lng,
            'headline' => $headline,
        ];
    }
}
