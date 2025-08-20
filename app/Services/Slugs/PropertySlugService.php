<?php

declare(strict_types=1);

namespace App\Services\Slugs;

use Illuminate\Support\Str;

class PropertySlugService
{
    /**
     * Build an SEO slug from address_single_line + postcode (district/sector).
     * Falls back gracefully if pieces are missing.
     */
    public static function makeSlug(?string $addressSingleLine, ?string $postcode): ?string
    {
        if ( ! $addressSingleLine && ! $postcode) {
            return null;
        }

        $base = mb_trim((string) $addressSingleLine);
        $slug = Str::slug($base, '-');

        // Append postcode district/sector (e.g., "B73" or "HG3")
        $sector = self::postcodeSector($postcode);
        if ($sector && ! Str::of($slug)->contains(Str::lower($sector))) {
            $slug = mb_trim($slug . '-' . Str::lower($sector), '-');
        }

        // Avoid empty string
        return '' !== $slug ? $slug : null;
    }

    /**
     * Create a short, stable 6-char token from provider UUID.
     * Uses unsigned crc32 -> base36 (deterministic, low collision risk).
     */
    public static function shortIdFromProvider(string $providerUuid, int $length = 6): string
    {
        $crc = sprintf('%u', crc32($providerUuid));        // decimal string
        $base36 = mb_str_pad(base_convert($crc, 10, 36), $length, '0', STR_PAD_LEFT);

        return mb_substr(mb_strtolower($base36), 0, $length);
    }

    /**
     * Extract postcode district/sector (e.g., "B73", "SW1A", "HG3").
     */
    private static function postcodeSector(?string $postcode): ?string
    {
        if ( ! $postcode) {
            return null;
        }
        $pc = mb_strtoupper(mb_trim($postcode));
        // UK postcode pattern (loose): split outward/inward on space; use outward part
        $parts = preg_split('/\s+/', $pc);

        return $parts[0] ?? $pc;
    }
}
