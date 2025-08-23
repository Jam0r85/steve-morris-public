<?php

declare(strict_types=1);

namespace App\Jobs\Street;

use App\Models\Branch;
use App\Traits\MakesStreetApiRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchBranchesFromStreet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, MakesStreetApiRequests, Queueable, SerializesModels;

    public function handle(): void
    {
        $cfg = config('services.street.open');
        $resp = $this->streetOpenGet((string) $cfg['url'], (string) $cfg['token'], '/branches');

        if ( ! $resp) {
            return;
        }

        foreach ($resp['data'] ?? [] as $branch) {
            $attr = $branch['attributes'] ?? [];
            $addr = $attr['address'] ?? [];

            Branch::updateOrCreate(
                ['uuid' => $branch['id']],
                [
                    'name' => $attr['name'] ?? null,
                    'public_name' => $attr['public_name'] ?? null,
                    'email_address' => $attr['email_address'] ?? null,
                    'telephone' => $attr['telephone'] ?? null,
                    'website' => $attr['website'] ?? null,
                    'address_single_line' => $addr['single_line'] ?? null,
                    'address_anon_single_line' => $addr['anon_single_line'] ?? null,
                    'address_building_number' => $addr['building_number'] ?? null,
                    'address_building_name' => $addr['building_name'] ?? null,
                    'address_street' => $addr['street'] ?? null,
                    'address_line_1' => $addr['line_1'] ?? null,
                    'address_line_2' => $addr['line_2'] ?? null,
                    'address_line_3' => $addr['line_3'] ?? null,
                    'address_line_4' => $addr['line_4'] ?? null,
                    'address_town' => $addr['town'] ?? null,
                    'address_country' => $addr['country'] ?? null,
                    'address_postcode' => $addr['postcode'] ?? null,
                    'address_udprn' => $addr['udprn'] ?? null,
                ]
            );
        }
    }
}
