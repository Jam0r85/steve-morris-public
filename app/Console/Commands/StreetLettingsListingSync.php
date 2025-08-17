<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Street\Lettings\UpsertStreetLettingsListingsChunk;
use App\Services\Street\StreetListingApiClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

final class StreetLettingsListingSync extends Command
{
    protected $signature = 'street:lettings:sync
        {--size= : Base page size (defaults to config)}
        {--includes= : Comma-separated includes (defaults to config)}
        {--limit= : Hard cap on TOTAL properties to fetch this run}
        {--areas= : Optional filter[areas]}
        {--postcode= : Optional filter[postcode]}
        {--min_price= : Optional filter[min_price]}
        {--max_price= : Optional filter[max_price]}
        {--min_bedrooms= : Optional filter[min_bedrooms]}
        {--status= : Optional filter[status] CSV (e.g. to_let,let_agreed)}';

    protected $description = 'Sync Street LETTINGS listings into local DB.';

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

        $lock = Cache::lock('street-lettings-sync-lock', 1800);
        if ( ! $lock->get()) {
            $this->warn('Lettings sync already running.');

            return self::SUCCESS;
        }

        $cfg = config('services.street.feed.lettings');
        $baseSize = (int) ($this->option('size') ?: $cfg['per_page']);
        $includes = $this->option('includes') ?? $cfg['includes'];

        // ----- Resolve limit -----
        $cliLimit = $this->option('limit');
        $configLimit = Config::get('services.street.feed.lettings.max_results');
        $limit = null;
        if (is_numeric($cliLimit)) {
            $limit = max(1, (int) $cliLimit);
        } elseif (is_numeric($configLimit)) {
            $limit = max(1, (int) $configLimit);
        } elseif (app()->environment(['local', 'testing'])) {
            $limit = 10;
        }
        $limited = null !== $limit;

        // Build filters from CLI
        $filters = array_filter([
            'include' => $includes,
            'filter[areas]' => $this->option('areas'),
            'filter[postcode]' => $this->option('postcode'),
            'filter[min_price]' => $this->option('min_price'),
            'filter[max_price]' => $this->option('max_price'),
            'filter[min_bedrooms]' => $this->option('min_bedrooms'),
            'filter[status]' => $this->option('status'), // CSV
        ], fn($v) => null !== $v && '' !== $v);

        $client = StreetListingApiClient::lettings();

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

                $resp = $client->list($page, $filters, $pageSizeOverride);

                $data = $resp['data'];
                $included = $resp['included'];
                $links = $resp['links'];
                $meta = $resp['meta'];

                if (empty($data)) {
                    break;
                }

                $hadPages = true;
                $chain[] = new UpsertStreetLettingsListingsChunk($data, $included);
                $fetched += count($data);

                foreach ($data as $res) {
                    if ( ! empty($res['id'])) {
                        $seen[] = $res['id'];
                    }
                }

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
                cache()->forever('street.lettings.last_successful_sync', now()->toIso8601String());
                $this->info('No LETTINGS pages returned. Timestamp bumped.');

                return self::SUCCESS;
            }

            // Same note as sales: deactivation should be done by a dedicated reconciliation job
            // if you want it per-channel. We skip it here by design.

            $chain[] = function () use ($limited, $fetched): void {
                cache()->forever('street.lettings.last_successful_sync', now()->toIso8601String());
                logger()->info('Street LETTINGS sync complete', ['limited' => $limited, 'fetched' => $fetched]);
            };

            Bus::chain($chain)->dispatch();

            $this->info(sprintf(
                'Street LETTINGS sync dispatched. %s fetched: %d%s',
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
