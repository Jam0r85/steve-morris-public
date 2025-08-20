<?php

declare(strict_types=1);

namespace App\Services\Street;

use Illuminate\Support\Facades\Http;

/**
 * Base JSON:API client for Street listing feeds.
 * Concrete clients (Sales/Lettings) supply their own endpoint path.
 */
class StreetListingApiClient
{
    public function __construct(
        private string $baseUrl,
        private string $token,
        private string $endpointPath,      // e.g. /sales/search or /lettings/search
        private int $perPage,
        private string $includes,
    ) {}

    /** Factory helpers */
    public static function sales(): self
    {
        $cfg = config('services.street.feed');

        return new self(
            baseUrl: $cfg['url'],
            token: $cfg['token'],
            endpointPath: $cfg['sales']['endpoint'],
            perPage: (int) $cfg['sales']['per_page'],
            includes: $cfg['sales']['includes'],
        );
    }

    public static function lettings(): self
    {
        $cfg = config('services.street.feed');

        return new self(
            baseUrl: $cfg['url'],
            token: $cfg['token'],
            endpointPath: $cfg['lettings']['endpoint'],
            perPage: (int) $cfg['lettings']['per_page'],
            includes: $cfg['lettings']['includes'],
        );
    }

    /**
     * Fetch a single page of listings.
     *
     * @param  array  $filters  (filter[xxx] params)
     * @param  int|null  $pageSizeOverride  override page[size] for this call (used for --limit)
     */
    public function list(int $page, array $filters = [], ?int $pageSizeOverride = null): array
    {
        $query = [
            'page[number]' => $page,
            'page[size]' => $pageSizeOverride ?? $this->perPage,
            'include' => $this->includes,
        ];

        // Merge JSON:API filter[...] params (already namespaced by caller)
        foreach ($filters as $k => $v) {
            $query[$k] = $v;
        }

        $resp = Http::withToken($this->token)
            ->retry(3, 500, throw: false)
            ->timeout(30)
            ->get(mb_rtrim($this->baseUrl, '/') . $this->endpointPath, $query)
            ->throw()
            ->json();

        return [
            'data' => (array) ($resp['data'] ?? []),
            'included' => (array) ($resp['included'] ?? []),
            'links' => (array) ($resp['links'] ?? []),
            'meta' => (array) ($resp['meta']['pagination'] ?? []),
        ];
    }
}
