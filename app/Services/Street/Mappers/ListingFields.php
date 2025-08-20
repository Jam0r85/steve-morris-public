<?php

declare(strict_types=1);

namespace App\Services\Street\Mappers;

use App\Services\Street\Support\IncludedIndex;
use Carbon\Carbon;

class ListingFields
{
    /** @return array{fields:array, updated_at:?Carbon} */
    public static function lettings(array $attributes, array $relationships, IncludedIndex $inc): array
    {
        $id = $relationships['lettingsListing']['data']['id'] ?? ($relationships['lettings_listing']['data']['id'] ?? null);
        $src = $id ? ($inc->get('lettings_listing', $id) ?? null) : null;
        $src = $src ?: $attributes;

        $fields = [
            'price_lettings' => array_key_exists('price_pcm', $src) ? $src['price_pcm'] : null,
            'rent_frequency' => array_key_exists('price_pcm', $src) ? 'pcm' : ($src['rent_frequency'] ?? ($src['frequency'] ?? null)),
            'deposit' => $src['deposit'] ?? null,
            'furnished' => $src['furnished'] ?? null,
        ];

        $updatedAt = ! empty($src['updated_at']) ? Carbon::parse($src['updated_at'])->utc() : null;

        return ['fields' => $fields, 'updated_at' => $updatedAt];
    }

    /** @return array{fields:array, updated_at:?Carbon} */
    public static function sales(array $attributes, array $relationships, IncludedIndex $inc): array
    {
        $id = $relationships['salesListing']['data']['id'] ?? ($relationships['sales_listing']['data']['id'] ?? null);
        $src = $id ? ($inc->get('sales_listing', $id) ?? null) : null;
        $src = $src ?: $attributes;

        $fields = [
            'price_sales' => $src['price'] ?? null,
            'price_qualifier' => $src['price_qualifier'] ?? null,
            'display_price' => array_key_exists('display_price', $src) ? (bool) $src['display_price'] : null,
        ];

        $updatedAt = ! empty($src['updated_at']) ? Carbon::parse($src['updated_at'])->utc() : null;

        return ['fields' => $fields, 'updated_at' => $updatedAt];
    }
}
