<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Street\Sales\UpsertStreetSalesListingsChunk;
use App\Services\Street\StreetListingApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

final class StreetSalesListingSync extends Command
{
    protected $signature = 'street:sales:sync
        {--size= : Base page size (defaults to config)}
        {--includes= : Comma-separated includes (defaults to config)}
        {--limit= : Hard cap on TOTAL properties to fetch this run}
        {--areas= : Optional filter[areas]}
        {--postcode= : Optional filter[postcode]}
        {--min_price= : Optional filter[min_price]}
        {--max_price= : Optional filter[max_price]}
        {--min_bedrooms= : Optional filter[min_bedrooms]}
        {--status= : Optional filter[status] CSV (e.g. for_sale,under_offer)}';

    protected $description = 'Sync Street SALES listings into local DB.';

    public function handle()
    {
        if ( ! config('services.street.feed.token')) {
            $this->error('Street Property Feed token is missing. Set STREET_FEED_API_TOKEN in .env');

            return self::FAILURE;
        }

        if ( ! config('services.street.feed.url')) {
            $this->error('Street Property Feed URL is missing. Set STREET_FEED_API_URL in .env');

            return self::FAILURE;
        }

        $lock = Cache::lock('street-sales-sync-lock', 1800);
        if ( ! $lock->get()) {
            $this->warn('Sales sync already running.');

            return self::SUCCESS;
        }

        $cfg = config('services.street.feed.sales');
        $baseSize = (int) ($this->option('size') ?: $cfg['per_page']);
        $includes = $this->option('includes') ?? $cfg['includes'];

        // ----- Resolve limit (CLI > config > default 10 in local/testing) -----
        $cliLimit = $this->option('limit');
        $configLimit = Config::get('services.street.feed.sales.max_results');
        $limit = null;
        if (is_numeric($cliLimit)) {
            $limit = max(1, (int) $cliLimit);
        } elseif (is_numeric($configLimit)) {
            $limit = max(1, (int) $configLimit);
        } elseif (app()->environment(['local', 'testing'])) {
            $limit = 10;
        }
        $limited = null !== $limit;

        // Build filters from CLI options
        $filters = array_filter([
            'include' => $includes,
            'filter[areas]' => $this->option('areas'),
            'filter[postcode]' => $this->option('postcode'),
            'filter[min_price]' => $this->option('min_price'),
            'filter[max_price]' => $this->option('max_price'),
            'filter[min_bedrooms]' => $this->option('min_bedrooms'),
            'filter[status]' => $this->option('status'), // CSV
            // NOTE: include_archived, branch, features etc can be added similarly
        ], fn($v) => null !== $v && '' !== $v);

        $client = StreetListingApiClient::sales();

        try {
            $page = 1;
            $chain = [];
            $seen = [];
            $hadPages = false;
            $fetched = 0;

            do {
                $pageSizeOverride = null;
                if ($limited) {
                    $remaining = max(0, $limit - $fetched);
                    if (0 === $remaining) {
                        break;
                    }
                    $pageSizeOverride = min($baseSize, $remaining);
                }

                // Client sets default includes; we override via $filters include
                $resp = $client->list($page, $filters, $pageSizeOverride);

                $data = $resp['data'];
                $included = $resp['included'];
                $links = $resp['links'];
                $meta = $resp['meta'];

                if (empty($data)) {
                    break;
                }

                $hadPages = true;
                $chain[] = new UpsertStreetSalesListingsChunk($data, $included);
                $fetched += count($data);

                foreach ($data as $res) {
                    if ( ! empty($res['id'])) {
                        $seen[] = $res['id'];
                    }
                }

                // Pagination
                $hasNext = ! empty($links['next']) && $links['next'] !== ($links['self'] ?? null);
                if ( ! $hasNext && ! empty($meta)) {
                    $hasNext = (($meta['current_page'] ?? $page) < ($meta['total_pages'] ?? $page));
                }
                if ($limited && $fetched >= $limit) {
                    $hasNext = false;
                }

                $page++;
            } while ($hasNext ?? false);

            if ( ! $hadPages) {
                cache()->forever('street.sales.last_successful_sync', now()->toIso8601String());
                $this->info('No SALES pages returned. Timestamp bumped.');

                return self::SUCCESS;
            }

            // SALES: we are *not* deactivating here automatically. If you want a periodic
            // full reconciliation/deactivation, add a dedicated nightly job to compare
            // current IDs vs database and mark missing as inactive (for SALES only).
            // Skipping here avoids cross-channel interference.

            // Final step: bump sync marker and log basics
            $chain[] = function () use ($limited, $fetched): void {
                cache()->forever('street.sales.last_successful_sync', now()->toIso8601String());
                logger()->info('Street SALES sync complete', ['limited' => $limited, 'fetched' => $fetched]);
            };

            Bus::chain($chain)->dispatch();

            $this->info(sprintf(
                'Street SALES sync dispatched. %s fetched: %d%s',
                $limited ? 'Limited run,' : 'Full run,',
                $fetched,
                $limited ? " (limit={$limit})" : '',
            ));
        } finally {
            $lock->release();
        }

        return self::SUCCESS;
    }
}
