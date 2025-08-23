<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Street\FetchListingsFromStreet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SyncStreetProperties extends Command
{
    protected $signature = 'street:sync:properties
        {--sales : Sync SALES listings}
        {--lettings : Sync LETTINGS listings}';

    protected $description = 'Sync Street SALES and/or LETTINGS property listings into the local DB.';

    public function handle(): int
    {
        // Guard against missing config
        if ( ! config('services.street.feed.token')) {
            $this->error('Street Property Feed token is missing. Set STREET_FEED_API_TOKEN in .env');

            return self::FAILURE;
        }

        if ( ! config('services.street.feed.url')) {
            $this->error('Street Property Feed URL is missing. Set STREET_FEED_API_URL in .env');

            return self::FAILURE;
        }

        $doSales = (bool) $this->option('sales');
        $doLettings = (bool) $this->option('lettings');

        // Default to both if no flags provided
        if ( ! $doSales && ! $doLettings) {
            $doSales = $doLettings = true;
        }

        if ($doSales) {
            $this->dispatchChannel('sales');
        }

        if ($doLettings) {
            $this->dispatchChannel('lettings');
        }

        return self::SUCCESS;
    }

    protected function dispatchChannel(string $channel): void
    {
        $lock = Cache::lock("street-{$channel}-sync-lock", 1800);
        if ( ! $lock->get()) {
            $this->warn(ucfirst($channel) . ' sync already running.');

            return;
        }

        try {
            FetchListingsFromStreet::dispatch($channel);
            $this->info("Street {$channel} sync dispatched...");

            // Try to read last result (from previous or after job finishes later)
            $result = cache("street.{$channel}.last_result");

            if ($result) {
                $status = $result['success'] ? 'SUCCESS' : 'FAILED';
                $this->info(sprintf(
                    'Street %s sync last run at %s: %d listings, status=%s',
                    mb_strtoupper($channel),
                    $result['at'],
                    $result['fetched'],
                    $status
                ));
            } else {
                $this->line("Street {$channel} sync result not yet available (queued).");
            }
        } finally {
            $lock->release();
        }
    }
}
