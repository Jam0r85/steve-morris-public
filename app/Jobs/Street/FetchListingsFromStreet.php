<?php

declare(strict_types=1);

namespace App\Jobs\Street;

use App\Mappers\StreetListingMapper;
use App\Models\Property;
use App\Models\PropertyMedia;
use App\Traits\MakesStreetApiRequests;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class FetchListingsFromStreet implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, MakesStreetApiRequests, Queueable, SerializesModels;

    public function __construct(
        protected string $channel // "sales" | "lettings"
    ) {}

    public function handle(): void
    {
        $fetched = 0;
        $pages = 0;
        $created = 0;
        $updated = 0;
        $success = false;

        try {
            $cfg = (array) config("services.street.feed.{$this->channel}");
            $endpoint = $cfg['endpoint'] ?? null;
            $perPage = (int) ($cfg['per_page'] ?? 200);

            if ( ! $endpoint) {
                throw new RuntimeException("No endpoint configured for Street channel [{$this->channel}]");
            }

            // Only pass includes Street actually allows here
            $allowedIncludes = [
                'branch',
                'rooms',
                'images',
                'floorplans',
                'featuresForPortals',
                'outsideSpaces',
                'parkingSpaces',
                'epc',
                'epc.pdf',
                'additionalMedia',
                'tags',
                'brochure',
                'development',
                'rooms.media',
            ];

            $configIncludes = (string) ($cfg['includes'] ?? 'images,floorplans,epc');

            $includesList = collect(explode(',', $configIncludes))
                ->map(fn ($s) => mb_trim($s))
                ->filter()
                ->unique()
                ->intersect($allowedIncludes)   // <- only keep allowed values
                ->values();

            $page = 1;
            $hasNext = true;

            while ($hasNext) {
                $query = [
                    'page' => ['number' => $page, 'size' => $perPage],
                    'include_archived' => 1, // ensure archived items are returned
                ];

                if ($includesList->isNotEmpty()) {
                    $query['include'] = $includesList->implode(',');
                }

                $resp = $this->streetFeedRequest('GET', $endpoint, $query);

                $data = $resp['data'] ?? [];
                $included = $resp['included'] ?? [];
                $links = $resp['links'] ?? [];
                $meta = $resp['meta'] ?? [];

                if (empty($data)) {
                    break;
                }

                $pages++;
                $mapper = new StreetListingMapper($this->channel, $included);

                foreach ($data as $listing) {
                    [$attributes, $mediaItems] = $mapper->map($listing);

                    DB::transaction(function () use ($attributes, $mediaItems, &$created, &$updated): void {
                        /** @var Property $prop */
                        $prop = Property::updateOrCreate(
                            ['provider_id' => $attributes['provider_id']],
                            $attributes
                        );

                        $prop->wasRecentlyCreated ? $created++ : $updated++;

                        // Refresh media for this property
                        if ( ! empty($mediaItems)) {
                            $prop->media()->delete();

                            foreach ($mediaItems as $m) {
                                PropertyMedia::create([
                                    'property_id' => $prop->id,
                                    'category' => $m['category'] ?? 'photo',
                                    'url' => $m['url'],
                                    'sort_order' => $m['sort_order'] ?? null,
                                    'is_image' => $m['is_image'] ?? null,
                                    'media_type' => $m['media_type'] ?? null,
                                    'title' => $m['title'] ?? null,
                                    'url_thumbnail' => $m['url_thumbnail'] ?? null,
                                    'url_small' => $m['url_small'] ?? null,
                                    'url_medium' => $m['url_medium'] ?? null,
                                    'url_large' => $m['url_large'] ?? null,
                                    'url_hero' => $m['url_hero'] ?? null,
                                    'url_full' => $m['url_full'] ?? null,
                                ]);
                            }
                        }
                    });

                    $fetched++;
                }

                // Pagination: prefer links.next; fallback to meta.pagination
                $hasNext = ( ! empty($links['next']) && $links['next'] !== ($links['self'] ?? null));
                if ( ! $hasNext && ! empty($meta)) {
                    $current = (int) ($meta['pagination']['current_page'] ?? $page);
                    $total = (int) ($meta['pagination']['total_pages'] ?? $page);
                    $hasNext = $current < $total;
                }

                $page++;
            }

            $success = true;

            Log::info("Street {$this->channel} listings sync OK", [
                'pages' => $pages,
                'fetched' => $fetched,
                'created' => $created,
                'updated' => $updated,
            ]);
        } catch (Throwable $e) {
            $success = false;

            Log::error("Street {$this->channel} listings sync FAILED", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'pages' => $pages,
                'fetched' => $fetched,
                'created' => $created,
                'updated' => $updated,
            ]);

            if (method_exists($this, 'notifyStreetFailure')) {
                $this->notifyStreetFailure(
                    subject: "Street {$this->channel} listings sync failed",
                    body: $e->getMessage()
                );
            }
        } finally {
            cache()->forever("street.{$this->channel}.last_result", [
                'at' => now()->toIso8601String(),
                'pages' => $pages,
                'fetched' => $fetched,
                'created' => $created,
                'updated' => $updated,
                'success' => $success,
            ]);
        }
    }
}
