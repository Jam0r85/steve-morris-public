<?php

declare(strict_types=1);

namespace App\Jobs\Street\Sales;

use App\Models\Property;
use App\Models\PropertyMedia;
use App\Models\PropertySlugRedirect;
use App\Services\Street\StreetListingMapper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class UpsertStreetSalesListingsChunk implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var array<int,array> JSON:API "property" resources for this chunk */
    public array $resources;

    /** @var array<int,array> raw "included" array from the API page */
    public array $included;

    public int $tries = 3;

    /**
     * @param  array<int,array>  $resources
     * @param  array<int,array>  $included
     */
    public function __construct(array $resources, array $included)
    {
        $this->resources = $resources;
        $this->included = $included; // mapper will build its own index
    }

    public function backoff(): array
    {
        return [5, 30, 120];
    }

    public function handle(): void
    {
        foreach ($this->resources as $res) {
            if (($res['type'] ?? '') !== 'property') {
                continue;
            }

            // Map Street feed → local arrays (property + media) for SALES channel
            ['property' => $pData, 'media' => $mData] =
                StreetListingMapper::mapResource($res, $this->included, 'sales');

            // Upsert property (by Street provider UUID)
            $prop = Property::firstWhere('provider_id', $pData['provider_id']);

            if ( ! $prop) {
                // New record
                $pData['first_seen_at'] = now();
                $prop = Property::create($pData);
            } else {
                // Existing record — reconcile slug_id and slug regardless of freshness
                $oldSlug = $prop->slug;

                if (empty($prop->slug_id) && ! empty($pData['slug_id'])) {
                    $prop->slug_id = $pData['slug_id'];
                }
                if ( ! empty($pData['slug']) && $pData['slug'] !== $prop->slug) {
                    if ( ! empty($oldSlug)) {
                        PropertySlugRedirect::firstOrCreate([
                            'old_slug' => $oldSlug,
                            'property_id' => $prop->id,
                        ]);
                    }
                    $prop->slug = $pData['slug'];
                }

                // Freshness: only overwrite everything if incoming provider_updated_at is newer
                if ( ! empty($pData['provider_updated_at'])) {
                    if ( ! $prop->provider_updated_at || $pData['provider_updated_at']->gt($prop->provider_updated_at)) {
                        $prop->fill($pData)->save();
                    } else {
                        // Not newer → BACKFILL certain fields if currently empty
                        $alwaysFillIfEmpty = [
                            'full_description',
                            'full_description_lettings',
                            'short_description',
                            'short_description_lettings',
                            'council_tax_band',
                            'council_tax_cost',
                            'service_charge',
                            'ground_rent',
                            'lease_expiry_date',
                            'heating_system',
                            'epc_rating',
                        ];
                        foreach ($alwaysFillIfEmpty as $k) {
                            if (array_key_exists($k, $pData) && (null === $prop->{$k} || '' === $prop->{$k})) {
                                $prop->{$k} = $pData[$k];
                            }
                        }
                        $prop->last_seen_at = now();
                        $prop->save(); // persist backfilled fields + slug/slug_id changes
                    }
                } else {
                    // No timestamp in payload → conservative update but do persist details
                    $prop->fill($pData)->save();
                }
            }

            // Upsert media (photos, floorplans, epc) with variants/metadata
            $order = 0;
            $currentUrls = [];
            foreach ($mData as $m) {
                if (empty($m['url'])) {
                    continue;
                }

                $currentUrls[] = $m['url'];

                PropertyMedia::updateOrCreate(
                    ['property_id' => $prop->id, 'url' => $m['url']],
                    [
                        'category' => $m['category'] ?? 'photo',
                        'sort_order' => $m['sort_order'] ?? $order++,
                        'width' => $m['width'] ?? null,
                        'height' => $m['height'] ?? null,

                        'is_image' => $m['is_image'] ?? null,
                        'media_type' => $m['media_type'] ?? null,
                        'title' => $m['title'] ?? null,

                        'url_thumbnail' => $m['url_thumbnail'] ?? null,
                        'url_small' => $m['url_small'] ?? null,
                        'url_medium' => $m['url_medium'] ?? null,
                        'url_large' => $m['url_large'] ?? null,
                        'url_hero' => $m['url_hero'] ?? null,
                        'url_full' => $m['url_full'] ?? null,
                    ],
                );
            }

            // Street-only media pruning (scoped via config/street.php)
            if (config('services.street.feed.prune_media')) {
                $currentUrls = array_values(array_unique($currentUrls));
                if (0 === count($currentUrls)) {
                    PropertyMedia::where('property_id', $prop->id)->delete();
                } else {
                    PropertyMedia::where('property_id', $prop->id)
                        ->whereNotIn('url', $currentUrls)
                        ->delete();
                }
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        $jobName = static::class;

        $message = "Street sync job failed!\n\n"
            . "Job: {$jobName}\n"
            . "Exception: {$exception->getMessage()}";

        Mail::raw($message, function ($mail): void {
            $mail->to(config('services.street.failed_jobs_email'))
                ->subject('⚠️ Street Sync Job Failed');
        });
    }
}
