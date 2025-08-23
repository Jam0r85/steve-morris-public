<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\Street\FetchBranchesFromStreet;
use Illuminate\Console\Command;

class SyncStreetBranches extends Command
{
    protected $signature = 'street:sync:branches';

    protected $description = 'Fetch and sync branches from Street API';

    public function handle(): void
    {
        $this->info('Dispatching branch sync job...');
        FetchBranchesFromStreet::dispatch();
        $this->info('Branch sync job dispatched.');
    }
}
