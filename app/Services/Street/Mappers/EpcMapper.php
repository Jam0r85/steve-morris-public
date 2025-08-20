<?php

declare(strict_types=1);

namespace App\Services\Street\Mappers;

use App\Services\Street\Support\IncludedIndex;
use Carbon\Carbon;

class EpcMapper
{
    /**
     * Extract EPC fields from the EPC relationship (if present).
     * Returns EPC rating and an updated_at (for freshness merging).
     *
     * @return array{epc_rating:?string, updated_at:?Carbon}
     */
    public static function map(array $relationships, IncludedIndex $inc): array
    {
        $epcId = $relationships['epc']['data']['id'] ?? null;
        if ( ! $epcId) {
            return ['epc_rating' => null, 'updated_at' => null];
        }

        $epc = $inc->get('epc', $epcId);
        if ( ! $epc) {
            return ['epc_rating' => null, 'updated_at' => null];
        }

        $rating = $epc['rating'] ?? null;
        $updated = ! empty($epc['updated_at']) ? Carbon::parse($epc['updated_at'])->utc() : null;

        return ['epc_rating' => $rating, 'updated_at' => $updated];
    }
}
