<?php

declare(strict_types=1);

namespace App\Services\Street\Mappers;

use App\Services\Street\Support\IncludedIndex;
use Carbon\Carbon;

final class DetailsMapper
{
    /**
     * Extract descriptions + extra details from included `details`.
     * Also looks inside `material_information` for lease_expiry_date (if present).
     *
     * @return array{
     *   description:?string, headline:?string, updated_at:?Carbon, features:array,
     *   council_tax_band:?string, council_tax_cost:?int,
     *   full_description:?string, full_description_lettings:?string,
     *   short_description:?string, short_description_lettings:?string,
     *   service_charge:?int, ground_rent:?int,
     *   lease_expiry_date:?string, heating_system:?string
     * }
     */
    public static function map(?string $channel, array $relationships, IncludedIndex $inc): array
    {
        $detailsId = $relationships['details']['data']['id'] ?? null;

        $out = [
            'description' => null,
            'headline' => null,
            'updated_at' => null,
            'features' => [],

            'council_tax_band' => null,
            'council_tax_cost' => null,

            'full_description' => null,
            'full_description_lettings' => null,
            'short_description' => null,
            'short_description_lettings' => null,

            'service_charge' => null,
            'ground_rent' => null,
            'lease_expiry_date' => null, // correct spelling
            'heating_system' => null,
        ];

        if ( ! $detailsId) {
            return $out;
        }

        $det = $inc->get('details', $detailsId);
        if ( ! $det) {
            return $out;
        }

        // Channel-aware best description (kept in 'description' for your generic field)
        $cands = [];
        if ('lettings' === $channel) {
            foreach (['full_description_lettings', 'short_description_lettings', 'full_description', 'short_description'] as $k) {
                if ( ! empty($det[$k])) {
                    $cands[] = $det[$k];
                }
            }
        } else {
            foreach (['full_description', 'short_description'] as $k) {
                if ( ! empty($det[$k])) {
                    $cands[] = $det[$k];
                }
            }
        }
        if ($cands) {
            usort($cands, fn($a, $b) => mb_strlen($b) <=> mb_strlen($a));
            $out['description'] = $cands[0];
        }

        // Map requested fields directly from details
        $out['council_tax_band'] = $det['council_tax_band'] ?? null;
        $out['council_tax_cost'] = isset($det['council_tax_cost']) ? (int) $det['council_tax_cost'] : null;

        $out['full_description'] = $det['full_description'] ?? null;
        $out['full_description_lettings'] = $det['full_description_lettings'] ?? null;
        $out['short_description'] = $det['short_description'] ?? null;
        $out['short_description_lettings'] = $det['short_description_lettings'] ?? null;

        $out['service_charge'] = isset($det['service_charge']) ? (int) $det['service_charge'] : null;
        $out['ground_rent'] = isset($det['ground_rent']) ? (int) $det['ground_rent'] : null;

        $out['heating_system'] = $det['heating_system'] ?? null;

        // Lease expiry: not in top-level details in your sample; look in material_information
        $mi = isset($det['material_information']) && is_array($det['material_information'])
            ? $det['material_information'] : null;

        if ($mi && ! empty($mi['lease_expiry_date'])) {
            $out['lease_expiry_date'] = $mi['lease_expiry_date']; // keep as Y-m-d; cast to date in model
        }

        // Headline and freshness
        if ( ! empty($det['location_summary'])) {
            $out['headline'] = $det['location_summary'];
        }
        if ( ! empty($det['updated_at'])) {
            $out['updated_at'] = Carbon::parse($det['updated_at'])->utc();
        }

        return $out;
    }
}
